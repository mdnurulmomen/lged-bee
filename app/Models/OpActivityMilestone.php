<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpActivityMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year_id',
        'duration_id',
        'outcome_id',
        'output_id',
        'activity_id',
        'title_en',
        'title_bn',
        'target_date',
        'assigned_budget',
    ];

    public function activity()
    {
        return $this->belongsTo(OpActivity::class, 'activity_id', 'id');
    }

    public function milestone_calendar()
    {
        return $this->hasOne(OpYearlyAuditCalendarActivity::class, 'milestone_id', 'id');
    }

    public function saveMilestone(array $data)
    {
        $this->create($data);
    }

    public function milestone_budget()
    {
        return $this->hasOne(ApOrganizationYearlyPlanBudget::class, 'milestone_id', 'id');
    }
}
