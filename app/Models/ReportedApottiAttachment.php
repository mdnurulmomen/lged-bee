<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportedApottiAttachment extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';
}
