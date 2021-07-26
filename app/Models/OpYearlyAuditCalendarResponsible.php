<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpYearlyAuditCalendarResponsible extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "office_id",
        "office_layer",
        "office_name_en",
        "office_name_bn",
        "short_name_en",
        "short_name_bn",
        "duration_id",
        "fiscal_year_id",
        "outcome_id",
        "output_id",
        "activity_id",
        "op_yearly_audit_calendar_id",
        "op_yearly_audit_calendar_activity_id",
        "remarks",
    ];

    public function office()
    {
        return $this->hasOne(XResponsibleOffice::class, 'office_id', 'office_id');
    }

    public function activities()
    {
        return $this->hasOne(OpActivity::class, 'id', 'activity_id');
    }
}
