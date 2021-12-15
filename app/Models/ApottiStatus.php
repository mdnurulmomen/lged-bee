<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApottiStatus extends Model
{
    protected $connection = 'OfficeDB';
    protected $table = 'apotti_status';
}
