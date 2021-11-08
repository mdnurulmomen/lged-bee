<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcQueryItem extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'ac_query_id',
        'item_title_en',
        'item_title_bn',
        'receiver_officer_id',
        'receiver_officer_name_bn',
        'receiver_officer_name_en',
        'receiver_unit_name_bn',
        'receiver_unit_name_en',
        'receiver_designation_name_bn',
        'receiver_designation_name_en',
        'status',
        'comment',
        'created_by',
        'updated_by'
    ];

}
