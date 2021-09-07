<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlan extends Model
{
    use HasFactory;

    protected $connection = "OfficeDB";

    protected $fillable = [
        'schedule_id',
        'activity_id',
        'milestone_id',
        'fiscal_year_id',
        'ministry_name_en',
        'ministry_name_bn',
        'ministry_id',
        'controlling_office_id',
        'controlling_office_en',
        'controlling_office_bn',
        'office_type',
        'total_unit_no',
        'nominated_offices',
        'nominated_office_counts',
        'subject_matter',
        'nominated_man_powers',
        'nominated_man_power_counts',
        'comment',
    ];

}
