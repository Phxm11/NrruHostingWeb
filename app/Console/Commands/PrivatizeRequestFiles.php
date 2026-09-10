<?php

namespace App\Console\Commands;

use App\Models\ServiceRequest;
use App\Support\RequestFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PrivatizeRequestFiles extends Command
{
    protected $signature = 'files:privatize {--dry-run : Report counts without moving files}';

    protected $description = 'Copy and verify legacy uploads into private storage before removing public copies';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('private');
        $paths = array_merge($public->allFiles('attachments'), $public->allFiles('signatures'));
        $moved = 0;
        foreach ($paths as $path) {
            if (! RequestFiles::isSafePath($path)) {
                $this->error('พบเส้นทางไฟล์ที่ไม่ถูกต้อง หยุดการย้ายไฟล์');

                return self::FAILURE;
            }
            if ($this->option('dry-run')) {
                continue;
            }
            $content = $public->get($path);
            if (! $private->exists($path)) {
                $private->put($path, $content);
            }
            if (! hash_equals(hash('sha256', $content), hash('sha256', $private->get($path)))) {
                $this->error('ไฟล์ปลายทางไม่ตรงกับต้นฉบับ หยุดโดยเก็บไฟล์ต้นฉบับไว้');

                return self::FAILURE;
            }
            if (! $public->delete($path)) {
                $this->error('คัดลอกสำเร็จ แต่ลบสำเนาสาธารณะไม่สำเร็จ กรุณาตรวจสิทธิ์โฟลเดอร์');

                return self::FAILURE;
            }
            $moved++;
        }
        $missing = 0;
        foreach (ServiceRequest::cursor() as $request) {
            foreach (RequestFiles::COLUMNS as $column) {
                $path = $request->{$column};
                if ($path && (! RequestFiles::isSafePath($path) || (! $private->exists($path) && ! $public->exists($path)))) {
                    $missing++;
                }
            }
        }
        $this->info('Public files: '.count($paths).'; moved: '.$moved.'; missing references: '.$missing);

        return $missing ? self::FAILURE : self::SUCCESS;
    }
}
