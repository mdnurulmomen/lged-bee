<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacMeetingWorksheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'pac_meeting_id',
        'worksheet_name',
        'worksheet_description',
        'comment',
        'created_by',
        'updated_by',
    ];
}
