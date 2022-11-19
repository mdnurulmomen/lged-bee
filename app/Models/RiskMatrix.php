<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskMatrix extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $table = 'risk_matrixes';

    protected $guarded = ['id'];

    public function riskAssessmentLivelihood()
    {
        return $this->belongsTo(XRiskAssessmentLikelihood::class, 'x_risk_assessment_likelihood_id', 'id');
    }

    public function riskAssessmentImpact()
    {
        return $this->belongsTo(XRiskAssessmentImpact::class, 'x_risk_assessment_impact_id', 'id');
    }

    public function riskLevel()
    {
        return $this->belongsTo(XRiskLevel::class, 'x_risk_level_id', 'id');
    }
}
