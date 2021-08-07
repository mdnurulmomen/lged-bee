<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputIndicator extends Model
{
    use HasFactory;

    public function details()
    {
        return $this->hasMany(OutputIndicatorDetail::class, 'output_indicator_id', 'id');
    }
}
