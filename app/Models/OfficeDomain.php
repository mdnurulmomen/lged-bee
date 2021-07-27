<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeDomain extends Model
{
    use HasFactory;

    protected $connection = 'DoptorDB';

    public function getOfficeDomains($office_ids = [])
    {
        if (!empty($office_ids)) {
            $query = static::whereIn('office_id', $office_ids)->where('status', 1)->get()->toArray();
            return $query;
        }
        return [];
    }
}
