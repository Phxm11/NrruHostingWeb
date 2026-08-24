<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // 'self_service'   = ผู้ใช้กรอกแบบฟอร์มขอใช้บริการจริง ผ่าน workflow อนุมัติ
            // 'legacy_import'  = นำเข้าจากระบบ Plesk เดิม ไม่มี form_no/purpose/approval จริง
            $table->enum('source', ['self_service', 'legacy_import'])
                ->default('self_service')
                ->after('status');
        });

        // form_no ของงานจริงต้องไม่ซ้ำกัน แต่ของ legacy เราไม่ได้สนใจความหมาย ให้เพิ่ม note field แทนตัว form_no ปลอม
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('legacy_note', 255)->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['source', 'legacy_note']);
        });
    }
};
