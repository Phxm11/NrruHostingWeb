<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeWhereEffectiveStatus(Builder $query, string $status): Builder
    {
        if ($status === 'active') {
            return $query
                ->where('service_accounts.status', 'active')
                ->where(function (Builder $query) {
                    $query->whereNull('service_accounts.expire_date')
                        ->orWhere('service_accounts.expire_date', '>=', today()->toDateString());
                });
        }

        if ($status === 'expired') {
            return $query
                ->where('service_accounts.status', '!=', 'disabled')
                ->where(function (Builder $query) {
                    $query->where('service_accounts.status', 'expired')
                        ->orWhere('service_accounts.expire_date', '<', today()->toDateString());
                });
        }

        return $query->where('service_accounts.status', $status);
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->status === 'active' && $this->expire_date && $this->expire_date < today()->toDateString()) {
            return 'expired';
        }

        return $this->status;
    }

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
