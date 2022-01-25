<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlanMain extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $table = 'annual_plan_main';
    protected $fillable = [
        'op_audit_calendar_event_id',
        'fiscal_year_id',
        'activity_id',
        'activity_type',
        'status',
        'approval_status',
    ];

    public function annual_plan_items()
    {
        return $this->hasMany(AnnualPlan::class, 'annual_plan_main_id', 'id');
    }
}
