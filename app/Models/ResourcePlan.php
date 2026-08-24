<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourcePlan extends Model
{
    protected $primaryKey = 'plan_id';
    public $timestamps = false;

    protected $fillable = [
        'service_type', 'size_label', 'cpu_vcpu', 'ram_gb',
        'storage_gb', 'fee_per_year', 'suitable_for',
    ];

    // Each request now stores its selected plan directly on service_requests.plan_id.
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'plan_id', 'plan_id');
    }
}
