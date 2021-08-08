<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpOutputIndicator extends Model
{
    use HasFactory;

    public function details()
    {
        return $this->hasMany(SpOutputIndicatorDetail::class, 'output_indicator_id', 'id');
    }

    public function output()
    {
        return $this->hasMany(XStrategicPlanOutput::class, 'id', 'output_id');
    }

    public function year()
    {
        return $this->hasOne(XFiscalYear::class, 'id', 'base_fiscal_year_id');
    }
}
