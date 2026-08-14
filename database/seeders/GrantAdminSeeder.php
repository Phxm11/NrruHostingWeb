<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ผูก role "admin" ให้กับบัญชีผู้ใช้ตามอีเมลที่กำหนด
 *
 * วิธีใช้:
 *   php artisan db:seed --class=GrantAdminSeeder
 *
 * หมายเหตุ: รันคำสั่งนี้บนเครื่อง/เซิร์ฟเวอร์ที่เชื่อมต่อกับฐานข้อมูลจริงของโปรเจกต์
 * ถ้ายังไม่มี role "admin" ในระบบ ให้รัน RolePermissionSeeder ก่อน:
 *   php artisan db:seed --class=RolePermissionSeeder
 */
class GrantAdminSeeder extends Seeder
{
    /**
     * อีเมลของบัญชีที่ต้องการให้เป็น admin
     */
    protected string $targetEmail = 'admin@nrru.ac.th';

    public function run(): void
    {
        $user = User::where('email', $this->targetEmail)->first();

        if (! $user) {
            $this->command->error("ไม่พบผู้ใช้ที่มีอีเมล: {$this->targetEmail}");
            return;
        }

        $adminRole = Role::where('name', 'admin')->first();

        if (! $adminRole) {
            $this->command->error('ไม่พบ role "admin" — กรุณารัน RolePermissionSeeder ก่อน');
            return;
        }

        $user->roles()->syncWithoutDetaching($adminRole);

        $this->command->info("ผูก role admin ให้กับ {$this->targetEmail} เรียบร้อยแล้ว");
    }
}
