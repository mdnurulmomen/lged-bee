<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RAir extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'parent_id',
        'report_number',
        'report_name',
        'fiscal_year_id',
        'annual_plan_id',
        'audit_plan_id',
        'activity_id',
        'air_description',
        'type',
        'is_sent',
        'status',
        'created_by',
        'modified_by',
    ];

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function annual_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'annual_plan_id', 'id');
    }

    public function ap_entities()
    {
        return $this->hasMany(AnnualPlanEntitie::class, 'annual_plan_id', 'annual_plan_id');
    }

    public function audit_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApEntityIndividualAuditPlan::class, 'audit_plan_id', 'id');
    }

    public function r_air_child(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RAir::class, 'id', 'parent_id');
    }

    public function latest_r_air_movement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RAirMovement::class)->latest();
    }
}
