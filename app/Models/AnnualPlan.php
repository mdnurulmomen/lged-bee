<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlan extends Model
{
    use HasFactory;

    protected $connection = "OfficeDB";

    protected $fillable = [
        'op_audit_calendar_event_id',
        'schedule_id',
        'activity_id',
        'milestone_id',
        'fiscal_year_id',
        'ministry_name_en',
        'ministry_name_bn',
        'ministry_id',
        'controlling_office_id',
        'controlling_office_en',
        'controlling_office_bn',
        'parent_office_name_en',
        'parent_office_name_bn',
        'parent_office_id',
        'office_type',
        'budget',
        'total_unit_no',
        'nominated_offices',
        'nominated_office_counts',
        'subject_matter',
        'nominated_man_powers',
        'nominated_man_power_counts',
        'comment',
        'status',
    ];

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

}
