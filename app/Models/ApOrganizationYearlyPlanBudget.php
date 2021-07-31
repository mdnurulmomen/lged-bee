<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApOrganizationYearlyPlanBudget extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

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
        'budget',
    ];
}
