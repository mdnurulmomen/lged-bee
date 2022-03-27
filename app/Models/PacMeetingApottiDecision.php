<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacMeetingApottiDecision extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function pac_apotti_decisions(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(PacApottiDecision::class, 'apotti_id', 'apotti_id');
    }
}
