<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlanEntitie extends Model
{
    use HasFactory;

    protected $connection = "OfficeDB";

    protected $fillable = [
        'annual_plan_id',
        'ministry_id',
        'ministry_name_bn',
        'ministry_name_en',
        'layer_id',
        'entity_id',
        'entity_name_bn',
        'entity_name_en',
        'nominated_offices',
    ];
}
