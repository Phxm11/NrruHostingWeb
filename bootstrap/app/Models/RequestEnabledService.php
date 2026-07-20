<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestEnabledService extends Model
{
    public $timestamps = false;

    protected $fillable = ['request_id', 'service_name', 'other_detail'];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
