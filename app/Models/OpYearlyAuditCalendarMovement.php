<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpYearlyAuditCalendarMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'op_yearly_calendar_id',
        'duration_id',
        'fiscal_year_id',
        'office_id',
        'unit_id',
        'unit_name_en',
        'unit_name_bn',
        'officer_type',
        'employee_id',
        'employee_designation_id',
        'employee_designation_en',
        'employee_designation_bn',
        'user_id',
        'calendar_status',
        'received_by',
        'sent_by',
        'created_by',
        'modified_by',
    ];
}
