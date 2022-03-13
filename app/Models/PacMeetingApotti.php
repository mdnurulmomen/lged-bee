<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacMeetingApotti extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function pac_meeting(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PacMeeting::class, 'pac_meeting_id', 'id');
    }
}
