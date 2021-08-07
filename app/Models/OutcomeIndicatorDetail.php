<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeIndicatorDetail extends Model
{
    use HasFactory;

    protected $fillable = ['duration_id', 'fiscal_year_id', 'outcome_id', 'unit_type', 'target_value'];

    public function indecator()
    {
        return $this->belongsTo(OutcomeIndicator::class, 'outcome_indicator_id', 'id');
    }
}
