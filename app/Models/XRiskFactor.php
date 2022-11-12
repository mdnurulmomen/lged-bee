<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XRiskFactor extends Model
{
    use HasFactory;

    public function riskFactorCriteria(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(XRiskFactorCriteria::class, 'x_risk_factor_id', 'id');
    }

    public function riskFactorRatings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(XRiskFactorRating::class, 'x_risk_factor_id', 'id');
    }
}
