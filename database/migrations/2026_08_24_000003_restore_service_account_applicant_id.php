<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Account administration queries this relationship directly.
        Schema::table('service_accounts', function (Blueprint $table) {
            $table->foreignId('applicant_id')->nullable()->after('request_id')->constrained('applicants', 'applicant_id');
        });

        DB::statement(
            'UPDATE service_accounts AS account '
            . 'INNER JOIN service_requests AS request ON request.request_id = account.request_id '
            . 'SET account.applicant_id = request.applicant_id'
        );
    }

    public function down(): void
    {
        Schema::table('service_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('applicant_id');
        });
    }
};
