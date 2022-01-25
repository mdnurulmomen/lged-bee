<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApPsrSubjectMatter extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $fillable = [
        'annual_plan_main_id',
        'annual_plan_id',
        'vumika',
        'subject_matter_en',
        'subject_matter_bn',
        'parent_id'

    ];
}
