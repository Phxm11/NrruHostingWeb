<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateStaff extends Command
{
    protected $signature = 'staff:create';

    protected $description = 'Create a staff account interactively without a default password';

    public function handle(): int
    {
        $data = [
            'name' => $this->ask('ชื่อเจ้าหน้าที่'),
            'email' => $this->ask('อีเมล'),
            'password' => $this->secret('รหัสผ่าน (อย่างน้อย 12 ตัวอักษร)'),
            'password_confirmation' => $this->secret('ยืนยันรหัสผ่าน'),
        ];
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create(collect($data)->only(['name', 'email', 'password'])->all() + ['is_active' => true]);
        $this->info('สร้างบัญชีเจ้าหน้าที่เรียบร้อยแล้ว');

        return self::SUCCESS;
    }
}
