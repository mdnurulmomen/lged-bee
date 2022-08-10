<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApEntityIndividualAuditPlan extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'annual_plan_id',
        'schedule_id',
        'activity_id',
        'milestone_id',
        'fiscal_year_id',
        'plan_description',
        'draft_office_id',
        'draft_unit_id',
        'draft_unit_name_en',
        'draft_unit_name_bn',
        'draft_designation_id',
        'draft_designation_name_en',
        'draft_designation_name_bn',
        'draft_officer_id',
        'draft_officer_name_en',
        'draft_officer_name_bn',
        'status',
        'has_office_order',
        'has_update_office_order',
        'edit_employee_id',
        'edit_user_details',
        'edit_time_start',
        'created_by',
        'modified_by',
        'device_type',
        'device_id',
    ];

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function annual_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'annual_plan_id', 'id');
    }

    public function ap_entities()
    {
        return $this->hasMany(AnnualPlanEntitie::class, 'annual_plan_id', 'annual_plan_id');
    }

    public function audit_teams(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AuditVisitCalendarPlanTeam::class, 'audit_plan_id', 'id');
    }

    public function air_reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RAir::class, 'audit_plan_id', 'id');
    }

    public function office_order(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApOfficeOrder::class, 'audit_plan_id', 'id')->where('approved_status','!=','log');
    }

    public function audit_team_update(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AuditVisitCalendarPlanTeamUpdate::class, 'audit_plan_id', 'id');
    }

    public function office_order_update(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApOfficeOrder::class, 'audit_plan_id', 'id')
            ->where('approved_status','draft');
    }

    public function office_order_log(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApOfficeOrder::class, 'audit_plan_id', 'id')
            ->where('approved_status','log');
    }
}
