<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApRiskAssessment extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';

    public function risk_assessment_items()
    {
        return $this->hasMany(ApRiskAssessmentItem::class);
    }
}
