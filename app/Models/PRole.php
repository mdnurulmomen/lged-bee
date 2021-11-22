<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRole extends Model
{
    use HasFactory;

    protected $fillable = ['role_name_en', 'role_name_bn', 'description_en', 'description_bn', 'user_level'];

    public function menu_actions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PMenuAction::class, 'p_menu_action_role_maps');
    }

    public function individual_menu_actions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PMenuAction::class, 'p_individual_action_permission_maps');
    }
}
