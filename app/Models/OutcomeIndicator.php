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
}
