<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApottiRAirMap extends Model
{
    use HasFactory;

    protected $connection = 'OfficeDB';
    protected $table = 'apotti_rair_maps';

    protected $fillable = [
        'apotti_id',
        'rairs_id',
        'is_delete',
        'created_by',
        'updated_by',
    ];

    public function apotti_map_data(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Apotti::class, 'apotti_id')->orderBy('onucched_no');
    }
}
