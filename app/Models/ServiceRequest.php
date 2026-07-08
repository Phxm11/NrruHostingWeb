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
    ];

    protected $casts = [
        'request_date' => 'date',
        'receipt_date' => 'date',
        'project_start_date' => 'date',
        'project_end_date' => 'date',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    public function developers()
    {
        return $this->hasMany(Developer::class, 'request_id', 'request_id');
    }

    public function requestResources()
    {
        return $this->hasMany(RequestResource::class, 'request_id', 'request_id');
    }

    public function enabledServices()
    {
        return $this->hasMany(RequestEnabledService::class, 'request_id', 'request_id');
    }

    public function techDetail()
    {
        return $this->hasOne(TechDetail::class, 'request_id', 'request_id');
    }

    public function domains()
    {
        return $this->hasMany(Domain::class, 'request_id', 'request_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'request_id', 'request_id');
    }

    public function feeCertification()
    {
        return $this->hasOne(FeeCertification::class, 'request_id', 'request_id');
    }

    public function policyAcceptance()
    {
        return $this->hasOne(PolicyAcceptance::class, 'request_id', 'request_id');
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
