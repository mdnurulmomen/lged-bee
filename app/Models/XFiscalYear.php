<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XFiscalYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration_id',
        'start',
        'end',
        'description'
    ];

}
