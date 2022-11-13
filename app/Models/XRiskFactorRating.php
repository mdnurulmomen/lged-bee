<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XRiskFactorRating extends Model
{
    use HasFactory;

    public function xRiskFactor()
    {
        return $this->belongsTo(XRiskFactor::class, 'x_risk_factor_id', 'id');
    }
}
