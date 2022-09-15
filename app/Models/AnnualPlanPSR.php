<?php

namespace App\Models;
use App\Models\AnnualPlan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlanPSR extends Model
{
    use HasFactory;

    protected $connection = "OfficeDB";

    protected $table = 'annual_plan_psrs';

    protected $filable = [
        'annual_plan_id',
        'activity_id',
        'fiscal_year_id',
        'plan_description',
        'status',
        'created_by',
        'modified_by',
    ];

    public function annual_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'annual_plan_id', 'id');
    }

}
