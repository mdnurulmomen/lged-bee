<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPSetting extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';
    protected $table = 'sp_settings';
    public $timestamps = false;
}
