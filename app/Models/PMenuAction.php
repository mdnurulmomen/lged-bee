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
        'activity_type',
        'class',
        'controller',
        'method',
        'icon',
        'display_order',
        'status',
        'parent_id',
        'menu_module_id',
        'action_menu_id',
        'is_other_module',
        'type',
        'created_by',
        'modified_by',
    ];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PMenuAction::class, 'parent_id');
    }

    public function menu_module(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PMenuAction::class, 'menu_module_id');
    }

    public function action_menu(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PMenuAction::class, 'action_menu_id');
    }

    public function module_childrens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('status', 1)->where('type', 'module')->orderBy('display_order', 'ASC')->with('module_childrens');
    }

    public function all_menu_action_children()
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('status', 1)->where('type', 'module')->orderBy('display_order', 'ASC')->with('module_children.menu.menu_children.menu_action.menu_action_children');
    }

    public function menus()
    {
        return $this->hasMany(PMenuAction::class, 'menu_module_id')->where('status', 1)->where('type', 'menu')->orderBy('display_order', 'ASC')->with('menu_childrens');
    }

    public function menu_childrens()
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('status', 1)->where('type', 'menu')->orderBy('display_order', 'ASC')->with('menu_childrens');
    }

    public function menu_actions()
    {
        return $this->hasMany(PMenuAction::class, 'action_menu_id')->where('status', 1)->where('type', 'action')->orderBy('display_order', 'ASC')->with('menu_action_childrens');
    }

    public function menu_action_childrens()
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('status', 1)->where('type', 'action')->orderBy('display_order', 'ASC')->with('menu_action_childrens');
    }


}
