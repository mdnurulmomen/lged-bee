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
        'team_parent_id',
        'fiscal_year_id',
        'duration_id',
        'outcome_id',
        'output_id',
        'activity_id',
        'milestone_id',
        'annual_plan_id',
        'audit_plan_id',
        'ministry_id',
        'ministry_name_bn',
        'ministry_name_en',
        'entity_id',
        'entity_name_bn',
        'entity_name_en',
        'cost_center_id',
        'cost_center_name_en',
        'cost_center_name_bn',
        'team_member_name_en',
        'team_member_name_bn',
        'team_member_designation_id',
        'team_member_office_id',
        'team_member_officer_id',
        'team_member_designation_en',
        'team_member_designation_bn',
        'team_member_role_en',
        'team_member_role_bn',
        'team_member_start_date',
        'team_member_end_date',
        'team_member_activity',
        'activity_man_days',
        'team_member_activity_description',
        'activity_location',
        'comment',
        'mobile_no',
        'approve_status',
        'status',
        'schedule_type',
        'sequence_level',
    ];

    public function activity()
    {
        return $this->belongsTo(OpActivity::class, 'activity_id', 'id');
    }

    public function x_fiscal_year()
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function plan_team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AuditVisitCalendarPlanTeam::class, 'team_id', 'id');
    }

    public function plan_parent_team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AuditVisitCalendarPlanTeam::class, 'team_parent_id', 'id');
    }

    public function annual_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'annual_plan_id', 'id');
    }

    public function office_order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApOfficeOrder::class, 'audit_plan_id', 'audit_plan_id');
    }
}
