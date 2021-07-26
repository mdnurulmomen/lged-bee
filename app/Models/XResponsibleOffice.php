<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XResponsibleOffice extends Model
{
    use HasFactory;

    public function calendar_responsible(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OpYearlyAuditCalendarResponsible::class, 'office_id', 'office_id');
    }
}
