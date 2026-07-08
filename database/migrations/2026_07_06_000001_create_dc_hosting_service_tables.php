<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id('applicant_id');
            $table->string('full_name', 150);
            $table->string('staff_or_student_id', 30);
            $table->string('unit_name', 150);
            $table->string('affiliation', 150);
            $table->string('position_title', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['staff_or_student_id', 'email']);
        });

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->string('form_no', 30);
            $table->date('request_date');
            $table->string('receipt_no', 30)->nullable();
            $table->date('receipt_date')->nullable();
            $table->time('receipt_time')->nullable();
            $table->foreignId('applicant_id')->constrained('applicants', 'applicant_id');
            $table->enum('purpose_type', [
                '1.1_teaching',
                '1.2_academic_research_community',
                '1.3_internal_admin',
                '1.4_other',
            ]);
            $table->text('purpose_other_detail')->nullable();
            $table->date('project_start_date');
            $table->date('project_end_date');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'expired'])
                ->default('submitted');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('developers', function (Blueprint $table) {
            $table->id('developer_id');
            $table->foreignId('request_id')->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->string('full_name', 150);
            $table->string('role_desc', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
        });

        Schema::create('resource_plans', function (Blueprint $table) {
            $table->id('plan_id');
            $table->enum('service_type', ['virtual_server', 'web_hosting']);
            $table->string('size_label', 30);
            $table->integer('cpu_vcpu')->nullable();
            $table->integer('ram_gb')->nullable();
            $table->integer('storage_gb')->nullable();
            $table->decimal('fee_per_year', 10, 2)->nullable();
            $table->string('suitable_for', 255)->nullable();
        });

        Schema::create('request_resources', function (Blueprint $table) {
            $table->id('request_resource_id');
            $table->foreignId('request_id')->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('resource_plans', 'plan_id');
            $table->integer('custom_cpu_vcpu')->nullable();
            $table->integer('custom_ram_gb')->nullable();
            $table->integer('custom_storage_gb')->nullable();
            $table->decimal('custom_fee', 10, 2)->nullable();
        });

        Schema::create('request_enabled_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->enum('service_name', ['ssh', 'http_https', 'database_access', 'other']);
            $table->string('other_detail', 255)->nullable();
        });

        Schema::create('tech_details', function (Blueprint $table) {
            $table->foreignId('request_id')->primary()->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->string('language_framework', 100)->nullable();
            $table->string('database_used', 100)->nullable();
            $table->string('port_service_needed', 255)->nullable();
            $table->boolean('needs_external_connection')->default(false);
        });

        Schema::create('department_codes', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('department_name', 150);
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->id('domain_id');
            $table->foreignId('request_id')->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->string('domain_name', 255);
            $table->string('domain_format', 255)->nullable();
            $table->string('department_code', 20)->nullable();
            $table->string('department_other', 150)->nullable();
            $table->foreign('department_code')->references('code')->on('department_codes');
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id('attachment_id');
            $table->foreignId('request_id')->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->enum('file_type', ['system_detail_doc', 'screenshot_evidence']);
            $table->string('file_path', 500);
            $table->timestamp('uploaded_at')->useCurrent();
        });

        Schema::create('fee_certifications', function (Blueprint $table) {
            $table->foreignId('request_id')->primary()->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->boolean('agree_to_pay')->default(false);
            $table->boolean('request_fee_waiver')->default(false);
            $table->text('waiver_reason')->nullable();
        });

        Schema::create('policy_acceptances', function (Blueprint $table) {
            $table->foreignId('request_id')->primary()->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->boolean('accepted')->default(false);
            $table->string('signature_image_path', 500)->nullable();
            $table->date('accepted_date')->nullable();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id('approval_id');
            $table->foreignId('request_id')->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->enum('approver_level', [
                'unit_head',
                'computer_center_deputy_director',
                'computer_center_director',
            ]);
            $table->string('approver_name', 150)->nullable();
            $table->enum('decision', [
                'certify_info_only',
                'certify_and_waive_fee',
                'acknowledge_assign_web_team',
                'rejected',
            ])->nullable();
            $table->string('signature_image_path', 500)->nullable();
            $table->date('decision_date')->nullable();
        });

        Schema::create('service_accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->foreignId('request_id')->constrained('service_requests', 'request_id')->cascadeOnDelete();
            $table->foreignId('applicant_id')->constrained('applicants', 'applicant_id');
            $table->string('username', 100)->unique();
            $table->string('password_hash', 255);
            $table->enum('account_type', ['ssh', 'database', 'control_panel', 'ftp'])->default('control_panel');
            $table->enum('status', ['active', 'disabled', 'expired'])->default('active');
            $table->string('created_by', 150)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->date('expire_date')->nullable();
            $table->dateTime('last_login')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_accounts');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('policy_acceptances');
        Schema::dropIfExists('fee_certifications');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('domains');
        Schema::dropIfExists('department_codes');
        Schema::dropIfExists('tech_details');
        Schema::dropIfExists('request_enabled_services');
        Schema::dropIfExists('request_resources');
        Schema::dropIfExists('resource_plans');
        Schema::dropIfExists('developers');
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('applicants');
    }
};
