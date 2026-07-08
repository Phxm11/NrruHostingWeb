<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentCode extends Model
{
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['code', 'department_name'];

    public function domains()
    {
        return $this->hasMany(Domain::class, 'department_code', 'code');
    }
}
