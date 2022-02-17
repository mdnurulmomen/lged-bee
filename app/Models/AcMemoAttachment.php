<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcMemoAttachment extends Model
{
    use SoftDeletes;

    protected $connection = 'OfficeDB';

    protected $fillable = [
        'ac_memo_id',
        'file_type',
        'file_user_define_name',
        'file_custom_name',
        'file_dir',
        'file_path',
        'file_size',
        'file_extension',
        'sequence',
        'created_by',
        'modified_by',
        'deleted_by',
    ];


    public function ac_memo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcMemo::class);
    }
}
