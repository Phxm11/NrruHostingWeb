<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('service_accounts', 'account_id')->cascadeOnDelete();
            $table->date('previous_expire_date')->nullable();
            $table->date('expire_date');
            $table->string('previous_status', 20);
            $table->foreignId('renewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('renewed_by_name', 255);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_renewals');
    }
};
