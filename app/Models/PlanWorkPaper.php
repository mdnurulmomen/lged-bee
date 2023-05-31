<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanWorkPaper extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'title_en',
        'title_bn',
        'attachment',
        'audit_plan_id',
        'yearly_plan_location_id',
        'created_by',
        'updated_by',
    ];


    public function auditPlan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApEntityIndividualAuditPlan::class, 'audit_plan_id', 'id');
    }

     public function yearly_plan_location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(YearlyPlanLocation::class, 'yearly_plan_location_id', 'id');
    }
}
