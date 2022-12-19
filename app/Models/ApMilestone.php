<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApMilestone extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';

    protected $fillable = [
        'id',
        'fiscal_year_id',
        'audit_plan_id',
        'annual_plan_id',
        'milestone_bn',
        'milestone_en',
        'activity_id',
        'milestone_id',
        'start_date',
        'milestone_target_date',
        'end_date',
    ];

    public function annual_plan()
    {
        return $this->belongsTo(AnnualPlan::class, 'annual_plan_id', 'id');
    }

    public function milestone()
    {
        return $this->belongsTo(OpActivityMilestone::class, 'milestone_id', 'id');
    }
}
