<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XAuditAssessmentCategory extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';

    protected $fillable = [
        'name_en',
        'name_bn',
        'status',
        'created_at',
        'updated_at',
    ];

}
