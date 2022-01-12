<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RAir extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'fiscal_year_id',
        'annual_plan_id',
        'audit_plan_id',
        'activity_id',
        'air_description',
        'type',
        'status',
        'created_by',
        'modified_by',
    ];

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'id', 'fiscal_year_id');
    }

    public function annual_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'id', 'annual_plan_id');
    }

    public function audit_plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApEntityIndividualAuditPlan::class, 'id', 'audit_plan_id');
    }
}
