<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApottiItem extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
}
