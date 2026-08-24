<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ตารางเชื่อม many-to-many: role 1 อันมีได้หลาย permission, permission 1 อันมอบให้ได้หลาย role
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id('permission_role_id');
            $table->foreignId('permission_id')->constrained('permissions', 'permission_id')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles', 'role_id')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['permission_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
