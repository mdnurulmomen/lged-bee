<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMenuAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en',
        'title_bn',
        'link',
        'class',
        'controller',
        'method',
        'icon',
        'display_order',
        'status',
        'parent_id',
        'is_other_module',
        'type',
        'created_by',
        'modified_by',
    ];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PMenuAction::class, 'parent_id');
    }
}
