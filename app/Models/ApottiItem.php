<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApottiItem extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';

    public static $memo_status_list = [
        '0' => 'N/A',
        '1' => 'নিস্পন্ন',
        '2' => 'অনিস্পন্ন',
        '3' => 'আংশিক নিস্পন্ন',
    ];

    protected $appends = ['memo_status_name'];

    public function getMemoStatusNameAttribute()
    {
        return self::$memo_status_list[$this->attributes['memo_status']];
    }

    public function fiscal_year(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(XFiscalYear::class, 'fiscal_year_id', 'id');
    }

    public function apotti_attachment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcMemoAttachment::class, 'ac_memo_id', 'memo_id')->where('file_type','broadsheet');
    }

}
