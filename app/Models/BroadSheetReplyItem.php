<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadSheetReplyItem extends Model
{
    use HasFactory;
    protected $connection = "OfficeDB";

    public function apotti(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ApottiItem::class, 'memo_id', 'memo_id');
    }

}
