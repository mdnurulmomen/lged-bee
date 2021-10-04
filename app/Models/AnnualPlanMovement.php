<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlanMovement extends Model
{
    use HasFactory;

    protected $connection = "OfficeDB";
    protected $fillable = [
        'fiscal_year_id',
        'op_audit_calendar_event_id',
        'duration_id',
        'outcome_id',
        'output_id',
        'annual_plan_id',
        'sender_office_id',
        'sender_office_name_en',
        'sender_office_name_bn',
        'sender_unit_id',
        'sender_unit_name_en',
        'sender_unit_name_bn',
        'sender_officer_id',
        'sender_name_en',
        'sender_name_bn',
        'sender_designation_id',
        'sender_designation_en',
        'sender_designation_bn',
        'receiver_type',
        'receiver_office_id',
        'receiver_office_name_en',
        'receiver_office_name_bn',
        'receiver_unit_id',
        'receiver_unit_name_en',
        'receiver_unit_name_bn',
        'receiver_officer_id',
        'receiver_name_en',
        'receiver_name_bn',
        'receiver_designation_id',
        'receiver_designation_en',
        'receiver_designation_bn',
        'status',
        'comments',
    ];
}
