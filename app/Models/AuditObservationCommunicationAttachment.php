<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditObservationCommunicationAttachment extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $fillable = ['file_name', 'file_location', 'file_type', 'file_url', 'tag'];

    public function communication()
    {
        return $this->belongsTo(AuditObservationCommunication::class, 'communication_id', 'id');
    }
}
