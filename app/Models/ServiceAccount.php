<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ServiceAccount extends Model
{
    protected $primaryKey = 'account_id';
    public $timestamps = false;

    protected $fillable = [
        'request_id', 'applicant_id', 'username', 'password',
        'account_type', 'status', 'created_by', 'expire_date',
    ];

    protected $hidden = ['password_hash'];

    // ใช้เมื่อสร้างบัญชี: ServiceAccount::create([...,'password' => 'plainpass'])
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = Hash::make($value);
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }
}
