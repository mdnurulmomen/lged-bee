<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditVisitCalendarPlanTeam extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $guarded = ['id'];

    /*
    protected $fillable = [
        // 'fiscal_year_id',
        // 'duration_id',
        // 'outcome_id',
        // 'output_id',
        // 'activity_id',
        // 'milestone_id',
        // 'annual_plan_id',
        // 'audit_plan_id',
        // 'ministry_id',
        // 'entity_id',
        // 'entity_name_en',
        // 'entity_name_bn',
        'team_name',
        'team_start_date',
        'team_end_date',
        'team_members',
        'team_schedules',
        'leader_name_en',
        'leader_name_bn',
        'leader_designation_id',
        'leader_designation_name_en',
        'leader_designation_name_bn',
        'team_parent_id',
        'activity_man_days',
        'audit_year_start',
        'audit_year_end',
        'yearly_plan_location_id',
        'approve_status'
        // 'approve_status',
        // 'controlling_office_id',
        // 'controlling_office_name_bn',
        // 'controlling_office_name_en',
    ];
    */

    public function members()
    {
        return $this->hasMany(AuditVisitCalenderPlanMember::class, 'team_id', 'id');
    }

    /*
    public function plan_member()
    {
        return $this->hasMany(AuditVisitCalenderPlanMember::class, 'team_id', 'id');
    }
    */

    public function child()
    {
        return $this->hasMany(AuditVisitCalendarPlanTeam::class, 'team_parent_id', 'id')->with('child');
    }

    public function parent()
    {
        return $this->belongsTo(AuditVisitCalendarPlanTeam::class, 'team_parent_id', 'id')->with('parent');
    }

    public function ac_memos(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcMemo::class, 'team_id', 'id');
    }

    public function annual_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'annual_plan_id', 'id');
    }

    public function yearly_plan_location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(YearlyPlanLocation::class, 'yearly_plan_location_id', 'id');
    }
}
