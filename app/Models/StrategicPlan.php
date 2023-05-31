<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrategicPlan extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';

    public function get_individual_plan()
    {
        return $this->hasMany(YearlyPlan::class, 'strategic_plan_year', 'strategic_plan_year');
    }
}
