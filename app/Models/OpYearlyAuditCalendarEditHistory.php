<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpYearlyAuditCalendarEditHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'op_yearly_calendar_id',
        'duration_id',
        'fiscal_year_id',
        'unit_id',
        'employee_id',
        'user_id',
        'employee_designation_id',
        'old_data',
    ];
}
