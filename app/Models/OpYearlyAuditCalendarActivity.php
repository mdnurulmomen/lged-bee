<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getTargetDateAttribute($value): string
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function setTargetDateAttribute($value)
    {
        if (strstr($value, '/')){
            $value = str_replace('/','-',$value);
        }

        $this->attributes['target_date'] = Carbon::parse($value)->format('Y-m-d');
    }
}
