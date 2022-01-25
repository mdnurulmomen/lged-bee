<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApPsrLineOfEnquire extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';
    protected $fillable = [
        'sub_objective_id',
        'line_of_enquire_en',
        'line_of_enquire_bn'

    ];
}
