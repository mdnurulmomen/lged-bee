<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RAirMovement extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'r_air_id',
        'sender_officer_id',
        'sender_office_id',
        'sender_unit_id',
        'sender_unit_name_en',
        'sender_unit_name_bn',
        'sender_employee_id',
        'sender_employee_name_en',
        'sender_employee_name_bn',
        'sender_employee_designation_id',
        'sender_employee_designation_en',
        'sender_employee_designation_bn',
        'sender_officer_phone',
        'sender_officer_email',
        'receiver_officer_id',
        'receiver_office_id',
        'receiver_unit_id',
        'receiver_unit_name_en',
        'receiver_unit_name_bn',
        'receiver_employee_id',
        'receiver_employee_name_en',
        'receiver_employee_name_bn',
        'receiver_employee_designation_id',
        'receiver_employee_designation_en',
        'receiver_employee_designation_bn',
        'receiver_officer_phone',
        'receiver_officer_email',
        'comments'
    ];
}
