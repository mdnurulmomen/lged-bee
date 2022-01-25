<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlanApproval extends Model
{
    use HasFactory;
    protected $connection = 'BeeCoreDB';
    protected $fillable = [
        'office_id',
        'office_en',
        'office_en',
        'fiscal_year_id',
        'op_audit_calendar_event_id',
        'annual_plan_main_id',
        'activity_type',
        'status',
        'approval_status'
    ];
}
