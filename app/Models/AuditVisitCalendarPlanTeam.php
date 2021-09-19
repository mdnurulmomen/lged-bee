<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditVisitCalendarPlanTeam extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
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
        'entity_name_en',
        'entity_name_bn',
        'team_name',
        'team_start_date',
        'team_end_date',
        'team_members',
        'leader_name_en',
        'leader_name_bn',
        'leader_designation_id',
        'leader_designation_name_en',
        'leader_designation_name_bn',
        'team_parent_id',
        'activity_man_days',
        'audit_year_start',
        'audit_year_end',
        'approve_status',
        'controlling_office_id',
        'controlling_office_name_bn',
        'controlling_office_name_en',
    ];

    public function plan_member()
    {
        return $this->hasMany(AuditVisitCalenderPlanMember::class, 'team_id', 'id');
    }
}
