<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApottiPorisishto extends Model
{
    use HasFactory;
    use \Awobaz\Compoships\Compoships;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'apotti_id',
        'memo_id',
        'details',
        'sequence',
        'print_type',
        'created_by',
        'created_at',
        'updated_at'
    ];
}
