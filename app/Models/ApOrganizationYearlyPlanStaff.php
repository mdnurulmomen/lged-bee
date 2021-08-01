<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApOrganizationYearlyPlanStaff extends Model
{
    use HasFactory;


    protected $connection = 'OfficeDB';
    protected $table = 'ap_organization_yearly_plan_staffs';

    protected $fillable = [
        'duration_id',
        'fiscal_year_id',
        'outcome_id',
        'output_id',
        'op_yearly_audit_calendar_id',
        'op_yearly_audit_calendar_activity_id',
        'schedule_id',
        'activity_id',
        'milestone_id',
        'ap_organization_yearly_plan_rp_id',
        'employee_id',
        'office_id',
        'unit_id',
        'unit_name_en',
        'unit_name_bn',
        'designation_id',
        'employee_name_en',
        'employee_name_bn',
        'employee_designation_en',
        'employee_designation_bn',
        'employee_grade',
        'employee_category',
        'task_start_date_plan',
        'task_end_date_plan',
    ];
}
