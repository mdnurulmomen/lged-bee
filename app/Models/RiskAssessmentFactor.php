<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskAssessmentFactor extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'item_id',
        'item_name_en',
        'item_name_bn',
        'item_type',
        'parent_office_id',
        'parent_office_name_en',
        'parent_office_name_bn',
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
