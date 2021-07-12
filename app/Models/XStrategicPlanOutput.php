<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XStrategicPlanOutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration_id',
        'outcome_id',
        'output_no',
        'output_title_en',
        'output_title_bn',
        'remarks',
    ];

    public function plan_outcome()
    {
        return $this->belongsTo(XstrategicPlanOutcome::class, 'outcome_id', 'id');
    }

    public function activities()
    {
        return $this->hasMany(OpActivity::class, 'output_id', 'id');
    }
}
