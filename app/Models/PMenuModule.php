<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PMenuModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_name_en',
        'module_name_bn',
        'module_link',
        'module_link',
        'module_class',
        'module_controller',
        'module_method',
        'is_other_module',
        'module_icon',
        'display_order',
        'status',
        'parent_module_id',
        'created_by',
        'modified_by',
    ];

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PRole::class, 'p_menu_module_role_maps');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PMenuModule::class, 'parent_module_id')->with('children');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PMenuModule::class, 'parent_module_id');
    }

    public function menus()
    {
        return $this->hasMany(PMenu::class, 'module_menu_id')->where('parent_menu_id', null);
    }
}
