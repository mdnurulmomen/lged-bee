<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyPlan extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';

    public function has_yearly_plan()
    {
        return $this->hasOne(ApEntityIndividualAuditPlan::class, 'yearly_plan_id', 'id');
    }
}
