<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpOrganizationYearlyAuditCalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'op_yearly_audit_calendar_id',
        'audit_calendar_data',
        'status',
    ];
}
