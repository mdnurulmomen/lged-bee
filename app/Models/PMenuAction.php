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
        'class',
        'controller',
        'method',
        'icon',
        'display_order',
        'status',
        'parent_id',
        'is_other_module',
        'type',
        'created_by',
        'modified_by',
    ];

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PMenuAction::class, 'parent_id');
    }

    public function module_children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('type', 'module')->orderBy('display_order', 'ASC')->with('module_children');
    }

    public function all_menu_action_children()
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('type', 'module')->orderBy('display_order', 'ASC')->with('module_children.menu.menu_children.menu_action.menu_action_children');
    }

    public function menu()
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('type', 'menu');
    }

    public function menu_children()
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('type', 'menu')->orderBy('display_order', 'ASC')->with('menu_children');
    }

    public function menu_action()
    {
        return $this->belongsTo(PMenuAction::class, 'parent_id')->where('type', 'action');
    }

    public function menu_action_children()
    {
        return $this->hasMany(PMenuAction::class, 'parent_id')->where('type', 'action')->orderBy('display_order', 'ASC')->with('menu_action_children');
    }


}
