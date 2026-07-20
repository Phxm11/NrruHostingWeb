<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestResource extends Model
{
    protected $primaryKey = 'request_resource_id';
    public $timestamps = false;

    protected $fillable = [
        'request_id', 'plan_id', 'custom_cpu_vcpu',
        'custom_ram_gb', 'custom_storage_gb', 'custom_fee',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }

    public function plan()
    {
        return $this->belongsTo(ResourcePlan::class, 'plan_id', 'plan_id');
    }
}
