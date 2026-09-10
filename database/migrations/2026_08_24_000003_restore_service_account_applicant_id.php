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
        if (! Schema::hasColumn('service_accounts', 'applicant_id')) {
            Schema::table('service_accounts', function (Blueprint $table) {
                $table->foreignId('applicant_id')->nullable()->after('request_id')->constrained('applicants', 'applicant_id');
            });
        }

        DB::table('service_accounts')->update([
            'applicant_id' => DB::raw('(SELECT applicant_id FROM service_requests WHERE service_requests.request_id = service_accounts.request_id)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('service_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('applicant_id');
        });
    }
};
