<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';
    protected $fillable = [
        'document_type',
        'relational_id',
        'fiscal_year',
        'is_main',
        'attachment_type',
        'attachment_description',
        'user_file_name',
        'file_custom_name',
        'file_location',
        'file_url', 'file_dir',
        'file_size_in_kb',
        'content_cover',
        'content_body',
        'meta_data',
        'created_by',
        'modified_by'
    ];


    public  $timestamps = false;

}
