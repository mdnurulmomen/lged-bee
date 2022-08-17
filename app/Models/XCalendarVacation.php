<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XCalendarVacation extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';
    public $timestamps = false;

    protected $fillable = [
        'year',
        'vacation_date',
        'details'
    ];
}
