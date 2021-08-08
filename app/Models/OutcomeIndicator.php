<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeIndicator extends Model
{
    use HasFactory;

    public function details()
    {
        return $this->hasMany(OutcomeIndicatorDetail::class, 'outcome_indicator_id', 'id');
    }

    public function outcome()
    {
        return $this->hasMany(XStrategicPlanOutcome::class, 'id', 'outcome_id');
    }

    public function year()
    {
        return $this->hasOne(XFiscalYear::class, 'id', 'base_fiscal_year_id');
    }
}
