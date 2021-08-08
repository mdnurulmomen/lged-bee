<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpYearlyAuditCalendarEditHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'op_yearly_calendar_id',
        'activity_id',
        'duration_id',
        'fiscal_year_id',
        'unit_id',
        'employee_id',
        'employee_name_en',
        'employee_name_bn',
        'user_id',
        'employee_designation_id',
        'employee_designation_en',
        'employee_designation_bn',
        'old_data',
    ];
}
