<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpActivityComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'comment_en',
        'comment_bn',
        'created_by',
        'modified_by',
    ];
}
