<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditVisitCalenderPlanMember extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'team_id',
        'fiscal_year_id',
        'duration_id',
        'outcome_id',
        'output_id',
        'activity_id',
        'milestone_id',
        'annual_plan_id',
        'audit_plan_id',
        'ministry_id',
        'entity_id',
        'cost_center_id',
        'cost_center_name_en',
        'cost_center_name_bn',
        'team_member_name_en',
        'team_member_name_bn',
        'team_member_designation_id',
        'team_member_designation_en',
        'team_member_designation_bn',
        'team_member_role_en',
        'team_member_role_bn',
        'team_member_start_date',
        'team_member_end_date',
        'team_member_activity',
        'team_member_activity_description',
        'activity_location',
        'comment',
        'mobile_no',
        'approve_status',
    ];

    public function plan_team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AuditVisitCalendarPlanTeam::class, 'team_id', 'id');
    }
}
