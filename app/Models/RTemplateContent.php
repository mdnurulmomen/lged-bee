<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RTemplateContent extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    public $timestamps = false;

    protected $fillable = [
        'relational_id',
        'template_type',
        'content_key',
        'content_value'
    ];
}
