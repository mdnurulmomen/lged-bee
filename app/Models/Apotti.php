<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apotti extends Model
{
    use HasFactory;
    protected $connection = 'OfficeDB';

    public function apotti_items(){
        return $this->hasMany(ApottiItem::class, 'apotti_id', 'id');
    }

    public function apotti_status(){
        return $this->hasMany(ApottiStatus::class, 'apotti_id', 'id');
    }
}
