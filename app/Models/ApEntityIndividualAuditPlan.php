<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApEntityIndividualAuditPlan extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'annual_plan_id',
        'schedule_id',
        'activity_id',
        'milestone_id',
        'fiscal_year_id',
        'plan_description',
        'draft_office_id',
        'draft_unit_id',
        'draft_unit_name_en',
        'draft_unit_name_bn',
        'draft_designation_id',
        'draft_designation_name_en',
        'draft_designation_name_bn',
        'draft_officer_id',
        'draft_officer_name_en',
        'draft_officer_name_bn',
        'status',
        'created_by',
        'modified_by',
        'device_type',
        'device_id',
    ];

    public function annual_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'annual_plan_id', 'id');
    }
}
