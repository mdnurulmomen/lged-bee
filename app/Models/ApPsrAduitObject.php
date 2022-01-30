<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApPsrAduitObject extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $fillable = [
        'annual_plan_main_id',
        'annual_plan_id',
        'audit_objective_en',
        'audit_objective_bn',
        'parent_id'

    ];

    public function line_of_enquiries()
    {
        return $this->hasMany(ApPsrLineOfEnquire::class, 'sub_objective_id', 'id');
    }
}
