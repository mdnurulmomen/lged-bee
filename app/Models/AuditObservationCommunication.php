<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditObservationCommunication extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    public function observation()
    {
        return $this->belongsTo(AuditObservation::class, 'observation_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(AuditObservationCommunicationAttachment::class, 'communication_id', 'id');
    }

    public function cc()
    {
        return $this->hasMany(AuditObservationCommunicationCC::class, 'communication_id', 'id');
    }
}
