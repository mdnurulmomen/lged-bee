<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApOfficeOrderMovement extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'ap_office_order_id',
        'annual_plan_id',
        'audit_plan_id',
        'office_id',
        'unit_id',
        'unit_name_en',
        'unit_name_bn',
        'officer_type',
        'employee_id',
        'employee_name_en',
        'employee_name_bn',
        'employee_designation_id',
        'employee_designation_en',
        'employee_designation_bn',
        'officer_phone',
        'officer_email',
        'received_by',
        'sent_by',
        'created_by',
        'modified_by'
    ];
}
