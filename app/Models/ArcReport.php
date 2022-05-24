<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArcReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_report_name',
        'year_from',
        'year_to',
        'ortho_bochor',
        'directorate_id',
        'directorate_en',
        'directorate_bn',
        'ministry_id',
        'ministry_name_bn',
        'ministry_name_en',
        'is_alochito',
        'created_by',
        'updated_by',
        'cover_page',
        'cover_page_path'
    ];
}
