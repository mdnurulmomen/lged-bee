<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPFile extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';
    protected $table = 'sp_files';
    public  $timestamps = false;
}
