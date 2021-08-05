<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpYearlyAuditCalendarActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'op_yearly_audit_calendar_id',
        'duration_id',
        'fiscal_year_id',
        'outcome_id',
        'output_id',
        'activity_id',
        'milestone_id',
        'target_date',
    ];

    public function milestones(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OpActivityMilestone::class, 'milestone_id', 'id');
    }
}
