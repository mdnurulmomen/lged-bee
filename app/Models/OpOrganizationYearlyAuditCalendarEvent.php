<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpOrganizationYearlyAuditCalendarEvent extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';

    protected $fillable = [
        'office_id',
        'op_yearly_audit_calendar_id',
        'audit_calendar_data',
        'activity_count',
        'milestone_count',
        'status',
        'approval_status',
    ];

    public function office()
    {
        return $this->hasOne(XResponsibleOffice::class, 'office_id', 'office_id');
    }

}
