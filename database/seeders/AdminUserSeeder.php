<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * แจ้งวิธีสร้างเจ้าหน้าที่โดยไม่กำหนดบัญชีหรือรหัสผ่านตายตัว
     */
    public function run(): void
    {
        if (User::count() > 0) {
            return;
        }

        $this->command?->info('สร้างบัญชีเจ้าหน้าที่ด้วย php artisan staff:create (ไม่มีรหัสผ่านเริ่มต้น)');
    }
}
