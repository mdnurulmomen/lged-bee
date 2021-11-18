<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMenuActionRoleMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'p_role_id',
        'p_menu_action_id',
    ];
}
