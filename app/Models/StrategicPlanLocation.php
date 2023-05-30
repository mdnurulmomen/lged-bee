<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrategicPlanLocation extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $fillable = [
        'strategic_plan_id',
        'strategic_plan_year',
        'project_id',
        'project_name_bn',
        'project_name_en',
        'function_id',
        'function_name_bn',
        'function_name_en',
        'parent_office_id',
        'parent_office_bn',
        'parent_office_en',
        'cost_center_id',
        'cost_center_en',
        'cost_center_bn',
        'location_no',
        'comment',
        'created_by',
        'updated_by',
    ];

    public function strategic_plans(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StrategicPlan::class, 'x_sp_duration_id' , 'strategic_plan_id');
    }
    public function get_individual_plan()
    {
        return $this->hasMany(YearlyPlan::class, 'strategic_plan_year', 'strategic_plan_year');
    }
}
