<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArcApottiAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'apotti_id',
        'attachment_type',
        'user_define_name',
        'attachment_name',
        'attachment_path',
        'cover_page_name',
        'cover_page_path',
        'cover_page',
        'deleted_by',
        'deleted_at',
        'rank'
    ];
}
