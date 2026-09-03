<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ลบคอลัมน์ last_login ออกจาก service_accounts — ตรวจสอบทั้งโปรเจกต์แล้ว
     * ไม่มีจุดไหนอ่านหรือเขียนค่านี้เลย (ไม่อยู่ใน $fillable ของ ServiceAccount ด้วย)
     * ไม่เคยมีการ track เวลาล็อกอินล่าสุดของบัญชีจริงจาก Plesk เข้ามาในระบบนี้
     */
    public function up(): void
    {
        Schema::table('service_accounts', function (Blueprint $table) {
            $table->dropColumn('last_login');
        });
    }

    public function down(): void
    {
        Schema::table('service_accounts', function (Blueprint $table) {
            $table->dateTime('last_login')->nullable();
        });
    }
};