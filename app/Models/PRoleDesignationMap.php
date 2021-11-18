<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRoleDesignationMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'p_role_id',
        'master_designation_id',
    ];
}
