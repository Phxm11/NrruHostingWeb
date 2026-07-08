<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $primaryKey = 'domain_id';
    public $timestamps = false;

    protected $fillable = [
        'request_id', 'domain_name', 'domain_format',
        'department_code', 'department_other',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }

    public function departmentCode()
    {
        return $this->belongsTo(DepartmentCode::class, 'department_code', 'code');
    }
}
