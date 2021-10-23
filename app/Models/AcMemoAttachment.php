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
        'attachment_type',
        'user_define_name',
        'attachment_name',
        'attachment_path',
        'sequence',
        'created_by',
        'modified_by',
        'deleted_by',
    ];

    protected $attachmentTypes = [
        'main',
        'porisishto',
        'pramanok',
        'other',
    ];

    public function ac_memo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcMemo::class);
    }
}
