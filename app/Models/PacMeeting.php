<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacMeeting extends Model
{
    use HasFactory;

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }
    public function meeting_members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PacMeetingMember::class, 'pac_meeting_id', 'id');
    }

    public function meeting_apottis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PacMeetingApotti::class, 'pac_meeting_id', 'id');
    }

}
