<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $primaryKey = 'applicant_id';
    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'staff_or_student_id',
        'unit_name',
        'affiliation',
        'position_title',
        'phone',
        'email',
    ];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'applicant_id', 'applicant_id');
    }

    public function serviceAccounts()
    {
        return $this->hasMany(ServiceAccount::class, 'applicant_id', 'applicant_id');
    }
}
