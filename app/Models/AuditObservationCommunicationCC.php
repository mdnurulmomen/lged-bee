<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditObservationCommunicationCC extends Model
{
    use HasFactory;

    protected $table = 'audit_observation_communication_cc';

    protected $fillable = ['communication_cc'];

    public function communication()
    {
        return $this->belongsTo(AuditObservationCommunication::class, 'communication_id', 'id');
    }
}
