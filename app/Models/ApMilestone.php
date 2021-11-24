<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApMilestone extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';

    public function milestone()
    {
        return $this->belongsTo(OpActivityMilestone::class, 'milestone_id', 'id');
    }
}
