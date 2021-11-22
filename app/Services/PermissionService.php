<?php

namespace App\Services;

use App\Models\PMenu;
use App\Models\PMenuAction;
use App\Models\PMenuActionRoleMap;
use App\Models\PMenuModule;
use App\Models\PMenuRoleMap;
use App\Models\PRole;
use App\Models\PRoleDesignationMap;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class PermissionService
{
    use GenericData, ApiHeart;

    public function permittedModules($master_designation_id): array
    {
        try {
            $roleDesignationMapId = PRoleDesignationMap::where('master_designation_id', $master_designation_id)->first();
            if (!empty($roleDesignationMapId)) {
                $role = PRole::where('master_designation_id', $roleDesignationMapId->p_role_id)->first();
                $roleMenuMapIds = PMenuActionRoleMap::where('p_role_id', $role->id)->pluck('p_menu_action_id');
                $modules = PMenuAction::where('is_other_module', 0)->whereIn('id', $roleMenuMapIds)->where('type', 'module')->with([
                    'module_childrens' => function ($query) use ($roleMenuMapIds) {
                        $query->where('type', 'module')->whereIn('id', $roleMenuMapIds);
                    }])->where('parent_id', null)->get();
            }
            return ['status' => 'success', 'data' => $modules];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function permittedOtherModules($master_designation_id): array
    {
        try {
            $roleDesignationMapId = PRoleDesignationMap::where('master_designation_id', $master_designation_id)->first();
            if (!empty($roleDesignationMapId)) {
                $role = PRole::where('master_designation_id', $roleDesignationMapId->p_role_id)->first();
                $roleMenuMapIds = PMenuActionRoleMap::where('p_role_id', $role->id)->pluck('p_menu_action_id');
                $modules = PMenuAction::where('is_other_module', 1)->whereIn('id', $roleMenuMapIds)->where('type', 'module')->with([
                    'module_childrens' => function ($query) use ($roleMenuMapIds) {
                        $query->where('type', 'module')->whereIn('id', $roleMenuMapIds);
                    }])->where('parent_id', null)->get();
            }
            return ['status' => 'success', 'data' => $modules];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function permittedModuleWiseMenus($master_designation_id, $module_link): array
    {
        try {
            $module = PMenuAction::where('link', $module_link)->first();
            $roleDesignationMapId = PRoleDesignationMap::where('master_designation_id', $master_designation_id)->first();
            if (!empty($roleDesignationMapId)) {
                $role = PRole::where('master_designation_id', $roleDesignationMapId->p_role_id)->first();
                $roleMenuMapIds = PMenuRoleMap::where('p_role_id', $role->id)->pluck('p_menu_action_id');
                $menus = PMenuAction::where('id', $module->id)->whereIn('id', $roleMenuMapIds)->where('type', 'menu')->with([
                    'menu_childrens' => function ($query) use ($roleMenuMapIds) {
                        $query->whereIn('id', $roleMenuMapIds)->where('type', 'menu');
                    }])->where('parent_id', null)->get();
            }
            $data = [
                'module' => $module,
                'menus' => $menus,
            ];
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAllMenuActionLists(): array
    {
        try {
            $data = PMenuAction::where('type', 'module')->where('parent_id', null)
                ->with(['menus', 'module_childrens.menus.menu_actions'])
                ->orderBy('display_order')->get();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function assignMenuActionsToRole(Request $request): array
    {
        try {
            $modules = json_decode($request->modules) ?: [];
            $menus = json_decode($request->menus) ?: [];
            $menu_actions = json_decode($request->menu_actions) ?: [];
            $all_actions = $modules + $menus + $menu_actions;
            $role_id = $request->role_id;
            $all_actions = PMenuAction::whereIn('id', $all_actions)->get();
            $role = PRole::find($role_id);
            $menuAssign = $role->menu_actions()->sync($all_actions);
            return ['status' => 'success', 'data' => 'success'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getIndividualPermittedModules($master_designation_id)
    {

    }

}
