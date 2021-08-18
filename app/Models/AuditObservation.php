<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditObservation extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    public function attachments()
    {
        return $this->hasMany(AuditObservationAttachment::class, 'observation_id', 'id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }
}
