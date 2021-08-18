<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'template_type',
        'content',
        'lang',
        'version',
        'status',
        'created_by',
        'modified_by',
    ];
}
