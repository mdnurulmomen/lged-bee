<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArcReportAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'attachment_type',
        'user_define_name',
        'attachment_name',
        'attachment_path',
        'cover_page_name',
        'cover_page_path',
        'cover_page',
    ];
}
