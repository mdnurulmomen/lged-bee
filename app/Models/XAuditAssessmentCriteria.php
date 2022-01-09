<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XAuditAssessmentCriteria extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';

    protected $fillable = [
        'category_id',
        'category_title_en',
        'category_title_bn',
        'name_en',
        'name_bn',
        'created_at',
        'updated_at',
    ];

}
