<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngagementLetter extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $table = 'engagement_letter';
    protected $fillable = [
        'audit_plan_id',
        'yearly_plan_location_id',
        'subject',
        'letter_to',
        'letter_from',
        'others',
        'created_by',
        'modified_by'
    ];
}
