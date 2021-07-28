<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApOrganizationYearlyPlanBudget extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'schedule_id',
        'activity_id',
        'milestone_id',
        'budget',
    ];
}
