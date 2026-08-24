<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ตารางเชื่อม many-to-many: user 1 คนมีได้หลาย role, role 1 อันมีได้หลาย user
        Schema::create('role_user', function (Blueprint $table) {
            $table->id('role_user_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles', 'role_id')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
