<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    protected $connection = 'BeeCoreDB';

    protected $fillable = [
        'cost_center_type_id',
        'query_title_en',
        'query_title_bn',
    ];

    public function cost_center_type()
    {
        return $this->belongsTo(CostCenterType::class, 'cost_center_type_id', 'id');
    }

    public function audit_query()
    {
        return $this->hasOne(AcQuery::class, 'query_id', 'id');
    }
}
