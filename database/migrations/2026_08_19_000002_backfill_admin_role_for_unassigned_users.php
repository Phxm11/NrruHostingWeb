<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * roles/permissions มีอยู่ในระบบมาตั้งแต่แรก แต่ไม่เคยมีโค้ดจุดไหนตรวจสิทธิ์จริง (ทุก route
     * ใน /admin กันด้วย auth middleware ตัวเดียว) ตอนนี้จะเพิ่ม middleware ให้บังคับใช้จริง
     *
     * ปัญหา: user_id=1 (Administrator) ไม่มีแถวใน role_user เลยสักแถว ถ้าเปิดบังคับสิทธิ์ทันที
     * จะทำให้บัญชีแอดมินตัวเองล็อกอินเข้า /admin ไม่ได้เลย จึงต้อง backfill ให้ก่อน:
     * ผู้ใช้คนไหนที่ยังไม่มี role เลย (สร้างไว้ก่อนที่ระบบ role จะถูกบังคับใช้จริง) ให้ถือว่าเป็น admin
     */
    public function up(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('role_id');
        if (! $adminRoleId) {
            return;
        }

        $usersWithoutRole = DB::table('users')
            ->whereNotIn('id', DB::table('role_user')->select('user_id'))
            ->pluck('id');

        foreach ($usersWithoutRole as $userId) {
            DB::table('role_user')->insert([
                'user_id' => $userId,
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // ไม่ย้อนกลับอัตโนมัติ เพราะแยกไม่ออกว่าแถวไหนที่ migration นี้เป็นคนเพิ่ม กับแถวที่ admin ตั้งเองทีหลัง
    }
};
