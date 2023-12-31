<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskAssessmentFactorItem extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'risk_assessment_factor_id',
        'x_risk_factor_id',
        'risk_factor_title_bn',
        'risk_factor_title_en',
        'factor_weight',
        'factor_rating',
        'comment',
        'attachment',
        'created_by',
        'update_by',
    ];
}
