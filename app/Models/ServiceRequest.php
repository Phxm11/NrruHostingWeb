<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $primaryKey = 'request_id';
    public $timestamps = false;

    protected $fillable = [
        'form_no',
        'request_date',
        'receipt_no',
        'receipt_date',
        'receipt_time',
        'applicant_id',
        'purpose_type',
        'purpose_other_detail',
        'project_start_date',
        'project_end_date',
        'status',
        'service_type',
        'plan_id',
        'custom_cpu_vcpu',
        'custom_ram_gb',
        'custom_storage_gb',
        'custom_fee',
        'enabled_services',
        'enabled_services_other_detail',
        'language_framework',
        'database_used',
        'port_service_needed',
        'needs_external_connection',
        'system_detail_doc_path',
        'screenshot_evidence_path',
        'agree_to_pay',
        'request_fee_waiver',
        'waiver_reason',
        'accepted',
        'signature_image_path',
        'accepted_date',
        'source',
        'legacy_note',
    ];

    protected $casts = [
        'request_date' => 'date',
        'receipt_date' => 'date',
        'project_start_date' => 'date',
        'project_end_date' => 'date',
        'enabled_services' => 'array',
        'needs_external_connection' => 'boolean',
        'agree_to_pay' => 'boolean',
        'request_fee_waiver' => 'boolean',
        'accepted' => 'boolean',
        'accepted_date' => 'date',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    public function developers()
    {
        return $this->hasMany(Developer::class, 'request_id', 'request_id');
    }

    // One request has one resource selection, so plan_id lives on this model.
    public function plan()
    {
        return $this->belongsTo(ResourcePlan::class, 'plan_id', 'plan_id');
    }

    public function domains()
    {
        return $this->hasMany(Domain::class, 'request_id', 'request_id');
    }


    public function approvals()
    {
        return $this->hasMany(Approval::class, 'request_id', 'request_id');
    }

    public function serviceAccounts()
    {
        return $this->hasMany(ServiceAccount::class, 'request_id', 'request_id');
    }
}
