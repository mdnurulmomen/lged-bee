<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcMemoPorisishto extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'ac_memo_id',
        'details',
        'sequence',
        'print_type',
        'created_by',
    ];
}
