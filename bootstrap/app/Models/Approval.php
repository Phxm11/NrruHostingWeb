<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $primaryKey = 'approval_id';
    public $timestamps = false;

    protected $fillable = [
        'request_id', 'approver_level', 'approver_name',
        'decision', 'signature_image_path', 'decision_date',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
