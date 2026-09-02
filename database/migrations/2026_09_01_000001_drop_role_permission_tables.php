<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เอาระบบ roles/permissions ออกทั้งหมด — ตอนนี้ผู้ใช้ที่ล็อกอินได้ (มีบัญชีใน users)
     * จะเข้าถึงหน้า admin ได้ทั้งหมด ไม่ต้องมีการกำหนด role/permission แยกอีกต่อไป
     *
     * ลบตารางเชื่อม (pivot) ก่อน แล้วค่อยลบตารางหลัก เพื่อไม่ให้ชนกับ foreign key
     */
    public function up(): void
    {
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }

    public function down(): void
    {
        // ไม่รองรับการย้อนกลับ เนื่องจากตั้งใจเลิกใช้ระบบ roles/permissions ถาวร
    }
};