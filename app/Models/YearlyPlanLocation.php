<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyPlanLocation extends Model
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
}
