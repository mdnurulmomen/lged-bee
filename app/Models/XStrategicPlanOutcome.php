<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XStrategicPlanOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration_id',
        'outcome_no',
        'outcome_title_en',
        'outcome_title_bn',
        'remarks',
    ];

    public function plan_duration()
    {
        return $this->belongsTo(XStrategicPlanDuration::class, 'duration_id', 'id');
    }

    public function plan_output()
    {
        return $this->hasMany(XStrategicPlanOutput::class, 'outcome_id', 'id');
    }

    public function indicators()
    {
        return $this->hasMany(OutcomeIndicator::class, 'outcome_id', 'id');
    }
}
