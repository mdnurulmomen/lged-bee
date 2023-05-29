<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XStrategicPlanDuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_year',
        'end_year',
        'remarks',
    ];

    public function plan_outcome()
    {
        return $this->hasMany(XStrategicPlanOutcome::class, 'duration_id', 'id');
    }
    public function strategic_plan()
    {
        return $this->hasMany(StrategicPlan::class, 'x_sp_duration_id', 'id');
    }
}
