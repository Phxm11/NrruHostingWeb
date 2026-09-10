<?php

// Uses the configured MySQL credentials, but creates and removes only its own temporary database.
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
if (is_file($root.'/bootstrap/cache/config.php')) {
    throw new RuntimeException('Run php artisan config:clear first.');
}
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
set_exception_handler(function (Throwable $exception) {
    fwrite(STDERR, 'MySQL test failed: '.$exception->getMessage().PHP_EOL);
    exit(1);
});
if (config('database.default') !== 'mysql') {
    throw new RuntimeException('This script requires a MySQL connection.');
}
$name = 'nrru_handover_test_'.date('YmdHis').'_'.bin2hex(random_bytes(3));
$connection = DB::connection();
$created = false;
$xmlPath = $root.'/storage/framework/'.$name.'.xml';
$exit = 1;
try {
    $connection->statement('CREATE DATABASE `'.$name.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $created = true;
    $xml = new DOMDocument;
    $xml->load($root.'/phpunit.xml');
    $xml->documentElement->setAttribute('bootstrap', $root.'/vendor/autoload.php');
    foreach ($xml->getElementsByTagName('env') as $env) {
        if ($env->getAttribute('name') === 'DB_CONNECTION') {
            $env->setAttribute('value', 'mysql');
        }
        if ($env->getAttribute('name') === 'DB_DATABASE') {
            $env->setAttribute('value', $name);
        }
    }
    foreach ($xml->getElementsByTagName('directory') as $directory) {
        $directory->nodeValue = $root.'/'.$directory->textContent;
    }
    $xml->save($xmlPath);
    $process = new Process([PHP_BINARY, $root.'/vendor/phpunit/phpunit/phpunit', '--configuration', $xmlPath], $root, [
        'APP_ENV' => 'testing', 'DB_CONNECTION' => 'mysql', 'DB_DATABASE' => $name,
        'DATABASE_URL' => '', 'DB_URL' => '',
    ]);
    $process->setTimeout(180);
    $exit = $process->run(function ($type, $buffer) {
        echo $buffer;
    });
} finally {
    if ($created && preg_match('/^nrru_handover_test_[0-9]{14}_[a-f0-9]{6}$/', $name)) {
        $connection->statement('DROP DATABASE `'.$name.'`');
    }
    if (is_file($xmlPath)) {
        unlink($xmlPath);
    }
}
exit($exit);
