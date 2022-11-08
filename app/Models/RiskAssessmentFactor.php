<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskAssessmentFactor extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $fillable = [
        'project_id',
        'project_name_en',
        'project_name_bn',
        'function_id',
        'function_name_en',
        'function_name_bn',
        'unit_master_id',
        'unit_master_name_bn',
        'unit_master_name_en',
        'parent_office_id',
        'parent_office_name_en',
        'parent_office_name_bn',
        'cost_center_id',
        'cost_center_name_en',
        'cost_center_name_bn',
        'total_risk_score',
        'risk_score_key',
        'is_latest',
        'created_by',
        'update_by',
    ];

    public function risk_factor_items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RiskAssessmentFactorItem::class, 'risk_assessment_factor_id', 'id');
    }
}
