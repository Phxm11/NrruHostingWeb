<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCertification extends Model
{
    protected $primaryKey = 'request_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['request_id', 'agree_to_pay', 'request_fee_waiver', 'waiver_reason'];

    protected $casts = [
        'agree_to_pay' => 'boolean',
        'request_fee_waiver' => 'boolean',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
