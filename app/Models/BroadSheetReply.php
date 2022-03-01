<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadSheetReply extends Model
{
    use HasFactory;
    protected $connection = "OfficeDB";

    public function broad_sheet_items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BroadSheetReplyItem::class,'broad_sheet_reply_id','id');
    }

    public function latest_broad_sheet_movement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BroadSheetMovement::class,'broad_sheet_id','id')->latest();
    }
}
