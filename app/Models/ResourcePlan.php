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

    public function requestResources()
    {
        return $this->hasMany(RequestResource::class, 'plan_id', 'plan_id');
    }
}
