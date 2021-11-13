<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_name_en',
        'menu_name_bn',
        'menu_class',
        'menu_link',
        'menu_controller',
        'menu_method',
        'menu_icon',
        'module_menu_id',
        'parent_menu_id',
        'display_order',
        'status',
        'created_by',
        'modified_by',
    ];

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PRole::class, 'p_menu_role_maps');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PMenu::class, 'parent_menu_id')->with('children');
    }
}
