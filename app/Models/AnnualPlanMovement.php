<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlanMovement extends Model
{
    use HasFactory;

    protected $connection = "OfficeDB";
    protected $fillable = [
        'schedule_id',
        'activity_id',
        'milestone_id',
        'fiscal_year_id',
        'annual_plan_id',
        'office_id',
        'unit_id',
        'unit_name_en',
        'unit_name_bn',
        'employee_id',
        'officer_type',
        'employee_designation_id',
        'employee_designation_en',
        'employee_designation_bn',
        'annual_plan_status',
        'received_by',
        'sent_by',
        'created_by',
        'modified_by',
    ];
}
