<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditAssessmentAreaRisk extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;
    protected $connection = 'OfficeDB';


    public function xRiskAssessmentImpact()
    {
        return $this->belongsTo(XRiskAssessmentImpact::class, 'x_risk_assessment_impact_id', 'id');
    }

    public function xRiskAssessmentLikelihood()
    {
        return $this->belongsTo(XRiskAssessmentLikelihood::class, 'x_risk_assessment_likelihood_id', 'id');
    }
}
