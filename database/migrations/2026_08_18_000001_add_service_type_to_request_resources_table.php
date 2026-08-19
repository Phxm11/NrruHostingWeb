<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ฟอร์มฝั่งผู้ใช้ให้เลือก service_type (virtual_server / web_hosting) และ
     * ถูก validate ไว้แล้ว แต่ไม่เคยมีคอลัมน์เก็บค่านี้ใน request_resources
     * ทำให้ข้อมูลหายเงียบ ๆ ทุกครั้งที่มีการส่งฟอร์ม จึงเพิ่มคอลัมน์นี้เข้ามา
     */
    public function up(): void
    {
        Schema::table('request_resources', function (Blueprint $table) {
            $table->enum('service_type', ['virtual_server', 'web_hosting'])
                ->nullable()
                ->after('request_id');
        });
    }

    public function down(): void
    {
        Schema::table('request_resources', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
    }
};
