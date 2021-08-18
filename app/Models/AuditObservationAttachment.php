<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditObservationAttachment extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $fillable = ['file_name', 'file_location', 'file_type', 'file_url', 'tag', 'file_category'];

    public function observation()
    {
        return $this->belongsTo(AuditObservation::class, 'observation_id', 'id');
    }
}
