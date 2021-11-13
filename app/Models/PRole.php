<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRole extends Model
{
    use HasFactory;

    protected $fillable = ['role_name_en', 'role_name_bn', 'description_en', 'description_bn', 'user_level'];

    public function menus(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PMenu::class, 'p_menu_role_maps');
    }

    public function modules(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PMenuModule::class, 'p_menu_module_role_maps');
    }
}
