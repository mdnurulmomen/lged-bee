<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XStrategicPlanRequiredCapacity extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration_id',
        'outcome_id',
        'capacity_no',
        'title_en',
        'title_bn',
        'remarks',
    ];

}
