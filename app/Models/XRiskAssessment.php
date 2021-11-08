<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XRiskAssessment extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';

    protected $fillable = [
        'risk_assessment_type',
        'company_type',
        'risk_assessment_title_bn',
        'risk_assessment_title_en',
    ];
}
