<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QacCommittee extends Model
{
    protected $connection = "OfficeDB";
    use HasFactory;

    public function qac_committee_members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(QacCommitteeMember::class, 'qac_committee_id', 'id')
            ->orderBy('officer_designation_grade','ASC');
    }
}
