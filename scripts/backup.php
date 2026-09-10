<?php

use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
set_exception_handler(function (Throwable $exception) {
    fwrite(STDERR, 'Backup failed: '.$exception->getMessage().PHP_EOL);
    exit(1);
});
if (config('database.default') !== 'mysql' || ! class_exists(ZipArchive::class)) {
    throw new RuntimeException('This backup script requires MySQL and the PHP zip extension.');
}
$settings = app('db')->connection()->getConfig();
$target = $root.'/storage/app/handover-backups/'.date('Ymd-His').'-'.bin2hex(random_bytes(3));
if (! mkdir($target, 0700, true)) {
    throw new RuntimeException('Cannot create backup directory.');
}
$dumpPath = $target.'/database.sql';
$process = new Process([
    $argv[1] ?? 'mysqldump', '--host='.$settings['host'], '--port='.$settings['port'],
    '--user='.$settings['username'], '--single-transaction', '--routines', '--events',
    '--default-character-set=utf8mb4', '--result-file='.$dumpPath, $settings['database'],
], $root, ['MYSQL_PWD' => $settings['password']]);
$process->setTimeout(180);
$process->mustRun();
if (! is_file($dumpPath) || filesize($dumpPath) === 0) {
    throw new RuntimeException('Empty database backup.');
}
$zip = new ZipArchive;
if ($zip->open($target.'/files.zip', ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
    throw new RuntimeException('Cannot create file backup.');
}
if (is_file($root.'/.env')) {
    $zip->addFile($root.'/.env', '.env');
}
foreach (['storage/app/private', 'storage/app/public'] as $directory) {
    if (! is_dir($root.'/'.$directory)) {
        continue;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$directory, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isFile() && ! $file->isLink()) {
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
            if (! $zip->addFile($file->getPathname(), $relative)) {
                throw new RuntimeException('Cannot add upload to backup.');
            }
        }
    }
}
if (! $zip->close()) {
    throw new RuntimeException('Cannot finish file backup.');
}
$verify = new ZipArchive;
if ($verify->open($target.'/files.zip', ZipArchive::CHECKCONS) !== true) {
    throw new RuntimeException('File backup verification failed.');
}
$count = $verify->numFiles;
$verify->close();
file_put_contents($target.'/SHA256SUMS', hash_file('sha256', $dumpPath)."  database.sql\n".hash_file('sha256', $target.'/files.zip')."  files.zip\n");
echo 'Backup saved: '.$target.PHP_EOL.'SQL bytes: '.filesize($dumpPath).'; archived files: '.$count.PHP_EOL;
