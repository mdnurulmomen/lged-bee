<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApOrganizationYearlyPlanResponsibleParty extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'schedule_id',
        'activity_id',
        'milestone_id',
        'party_id',
        'party_name_en',
        'party_name_bn',
        'party_type',
        'ministry_id',
        'ministry_name_en',
        'ministry_name_bn',
        'task_start_date_plan',
        'task_end_date_plan',
    ];
}
