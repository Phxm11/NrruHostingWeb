<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    protected $primaryKey = 'developer_id';
    public $timestamps = false;

    protected $fillable = ['request_id', 'full_name', 'role_desc', 'phone', 'email'];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
