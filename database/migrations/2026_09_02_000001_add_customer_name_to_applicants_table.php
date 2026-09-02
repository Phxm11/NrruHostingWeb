<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มฟิลด์ Customer Name (ชื่อบัญชีลูกค้าที่ใช้อ้างอิงตอนสร้างบัญชีใน Plesk เช่น
     * "web develop", "edu-eduroom", "human") — คนละอย่างกับชื่อ-สกุลผู้ขอใช้บริการ (full_name)
     */
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('customer_name', 150)->nullable()->after('full_name');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('customer_name');
        });
    }
};
