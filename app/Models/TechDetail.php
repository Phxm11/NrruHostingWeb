<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechDetail extends Model
{
    protected $primaryKey = 'request_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'request_id', 'language_framework', 'database_used',
        'port_service_needed', 'needs_external_connection',
    ];

    protected $casts = [
        'needs_external_connection' => 'boolean',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
