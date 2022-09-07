<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApottiCategory extends Model
{
    protected $connection = 'BeeCoreDB';

    protected $fillable = [
        'name_bn',
        'name_en',
        'parent_id',
        'directorate_id',
    ];
}
