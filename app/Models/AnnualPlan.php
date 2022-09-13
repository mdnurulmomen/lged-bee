<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlan extends Model
{
    use HasFactory;

    protected $connection = "OfficeDB";

    protected $fillable = [
        'annual_plan_main_id',
        'op_audit_calendar_event_id',
        'schedule_id',
        'activity_id',
        'activity_type',
        'milestone_id',
        'fiscal_year_id',
        'office_type',
        'office_type_id',
        'office_type_en',
        'annual_plan_type',
        'thematic_title',
        'budget',
        'cost_center_total_budget',
        'total_expenditure',
        'total_unit_no',
        'nominated_office_counts',
        'subject_matter',
        'sub_subject_matter',
        'vumika',
        'audit_objective',
        'audit_approach',
        'nominated_man_powers',
        'nominated_man_power_counts',
        'comment',
        'status',
        'project_id',
        'project_name_bn',
        'project_name_en',
        'is_revised',
        'is_revised_delete',
        'created_by',
        'updated_by',
    ];

    public function annual_plan_main(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlanMain::class, 'annual_plan_main_id', 'id');
    }

    public function yearly_audit_calendar_event_schedule(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OpOrganizationYearlyAuditCalendarEventSchedule::class, 'milestone_id', 'activity_milestone_id');
    }

    public function activity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OpActivity::class, 'activity_id', 'id');
    }

    public function milestone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OpActivityMilestone::class, 'milestone_id', 'id');
    }

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function audit_plans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApEntityIndividualAuditPlan::class, 'annual_plan_id', 'id');
    }

    public function ap_entities()
    {
        return $this->hasMany(AnnualPlanEntitie::class, 'annual_plan_id', 'id');
    }

    public function ap_milestones()
    {
        return $this->hasMany(ApMilestone::class, 'annual_plan_id', 'id');
    }

    public function office_order(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApOfficeOrder::class, 'annual_plan_id', 'id')->where('audit_plan_id', 0);
    }
     public function annual_plan_psr(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlanPSR::class, 'id', 'annual_plan_id');
    }
}
