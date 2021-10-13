<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditPlanTeamInfo extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';
    protected $fillable = [
        'fiscal_year_id',
        'duration_id',
        'outcome_id',
        'output_id',
        'office_id',
        'total_teams',
        'total_team_members',
        'total_employees',
        'total_working_days',
    ];
}
