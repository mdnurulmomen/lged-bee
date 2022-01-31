<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QacCommitteeAirMap extends Model
{
    protected $connection = "OfficeDB";
    use HasFactory;

    public function committee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(QacCommittee::class, 'qac_committee_id', 'id');
    }
}
