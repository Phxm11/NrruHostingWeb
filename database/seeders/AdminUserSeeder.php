<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * สร้างบัญชีผู้ดูแลระบบเริ่มต้น (หากยังไม่มีผู้ใช้ในระบบ)
     * คำเตือน: ให้เปลี่ยนรหัสผ่านหลังจากเข้าสู่ระบบครั้งแรก
     */
    public function run(): void
    {
        if (User::count() > 0) {
            return;
        }

        User::create([
            'name'              => 'Administrator',
            'email'             => 'admin@nrru.ac.th',
            'password'          => 'password',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
    }
}
