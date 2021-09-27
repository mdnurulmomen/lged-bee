<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApOfficeOrder extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'annual_plan_id', 'schedule_id', 'activity_id', 'milestone_id', 'fiscal_year_id',
        'audit_plan_id', 'duration_id', 'outcome_id', 'output_id', 'memorandum_no',
        'memorandum_date', 'heading_details', 'advices', 'approved_status', 'order_cc_list',
        'team_members','team_schedules','draft_officer_id', 'draft_officer_name_en', 'draft_officer_name_bn', 'draft_designation_id',
        'draft_designation_name_en', 'draft_designation_name_bn', 'created_by', 'modified_by'
    ];
}
