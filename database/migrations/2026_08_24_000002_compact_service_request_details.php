<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Consolidate data that has exactly one value-set per service request.
     *
     * Developers, domains, approvals, and service accounts remain separate
     * because each request can legitimately have more than one of those rows.
     */
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Resource selection: the public form permits one selection per request.
            $table->enum('service_type', ['virtual_server', 'web_hosting'])->nullable()->after('status');
            $table->foreignId('plan_id')->nullable()->after('service_type')->constrained('resource_plans', 'plan_id');
            $table->integer('custom_cpu_vcpu')->nullable()->after('plan_id');
            $table->integer('custom_ram_gb')->nullable()->after('custom_cpu_vcpu');
            $table->integer('custom_storage_gb')->nullable()->after('custom_ram_gb');
            $table->decimal('custom_fee', 10, 2)->nullable()->after('custom_storage_gb');

            // A small, fixed set of requested services is stored as a JSON array.
            $table->json('enabled_services')->nullable()->after('custom_fee');
            $table->string('enabled_services_other_detail', 255)->nullable()->after('enabled_services');

            // These fields were previously split into three 1:1 tables.
            $table->string('language_framework', 100)->nullable()->after('enabled_services_other_detail');
            $table->string('database_used', 100)->nullable()->after('language_framework');
            $table->string('port_service_needed', 255)->nullable()->after('database_used');
            $table->boolean('needs_external_connection')->default(false)->after('port_service_needed');
            $table->string('system_detail_doc_path', 500)->nullable()->after('needs_external_connection');
            $table->string('screenshot_evidence_path', 500)->nullable()->after('system_detail_doc_path');
            $table->boolean('agree_to_pay')->default(false)->after('screenshot_evidence_path');
            $table->boolean('request_fee_waiver')->default(false)->after('agree_to_pay');
            $table->text('waiver_reason')->nullable()->after('request_fee_waiver');
            $table->boolean('accepted')->default(false)->after('waiver_reason');
            $table->string('signature_image_path', 500)->nullable()->after('accepted');
            $table->date('accepted_date')->nullable()->after('signature_image_path');
        });

        // Copy existing records before removing the old, 1:1 detail tables.
        foreach (DB::table('request_resources')->orderBy('request_resource_id')->get() as $row) {
            DB::table('service_requests')->where('request_id', $row->request_id)->update([
                'service_type' => $row->service_type,
                'plan_id' => $row->plan_id,
                'custom_cpu_vcpu' => $row->custom_cpu_vcpu,
                'custom_ram_gb' => $row->custom_ram_gb,
                'custom_storage_gb' => $row->custom_storage_gb,
                'custom_fee' => $row->custom_fee,
            ]);
        }

        foreach (DB::table('request_enabled_services')->orderBy('id')->get()->groupBy('request_id') as $requestId => $services) {
            DB::table('service_requests')->where('request_id', $requestId)->update([
                'enabled_services' => json_encode($services->pluck('service_name')->values()->all()),
                'enabled_services_other_detail' => optional($services->firstWhere('service_name', 'other'))->other_detail,
            ]);
        }

        foreach (DB::table('tech_details')->get() as $row) {
            DB::table('service_requests')->where('request_id', $row->request_id)->update((array) $row);
        }
        foreach (DB::table('fee_certifications')->get() as $row) {
            DB::table('service_requests')->where('request_id', $row->request_id)->update([
                'agree_to_pay' => $row->agree_to_pay,
                'request_fee_waiver' => $row->request_fee_waiver,
                'waiver_reason' => $row->waiver_reason,
            ]);
        }
        foreach (DB::table('policy_acceptances')->get() as $row) {
            DB::table('service_requests')->where('request_id', $row->request_id)->update([
                'accepted' => $row->accepted,
                'signature_image_path' => $row->signature_image_path,
                'accepted_date' => $row->accepted_date,
            ]);
        }
        foreach (DB::table('attachments')->get() as $row) {
            $column = $row->file_type === 'system_detail_doc' ? 'system_detail_doc_path' : 'screenshot_evidence_path';
            DB::table('service_requests')->where('request_id', $row->request_id)->update([$column => $row->file_path]);
        }

        // applicant_id is derivable through service_accounts.request_id -> service_requests.
        Schema::table('service_accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('applicant_id');
        });

        Schema::dropIfExists('policy_acceptances');
        Schema::dropIfExists('fee_certifications');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('tech_details');
        Schema::dropIfExists('request_enabled_services');
        Schema::dropIfExists('request_resources');
    }

    public function down(): void
    {
        // The original migration defines the detailed-table schemas. This rollback
        // restores the parent columns and account relation only; it does not split
        // data back into the retired tables.
        Schema::table('service_accounts', function (Blueprint $table) {
            $table->foreignId('applicant_id')->nullable()->after('request_id')->constrained('applicants', 'applicant_id');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'service_type', 'plan_id', 'custom_cpu_vcpu', 'custom_ram_gb', 'custom_storage_gb', 'custom_fee',
                'enabled_services', 'enabled_services_other_detail', 'language_framework', 'database_used',
                'port_service_needed', 'needs_external_connection', 'system_detail_doc_path', 'screenshot_evidence_path',
                'agree_to_pay', 'request_fee_waiver', 'waiver_reason', 'accepted', 'signature_image_path', 'accepted_date',
            ]);
        });
    }
};
