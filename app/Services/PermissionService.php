<?php

namespace App\Services;

use App\Models\PMenu;
use App\Models\PMenuModule;
use App\Models\PMenuModuleRoleMap;
use App\Models\PMenuRoleMap;
use App\Models\PRole;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class PermissionService
{
    use GenericData, ApiHeart;

    public function permittedModules($master_designation_id): array
    {
        try {
            $role = PRole::where('master_designation_id', $master_designation_id)->first();
            $roleMenuMapIds = PMenuModuleRoleMap::where('p_role_id', $role->id)->pluck('p_menu_module_id');
            $modules = PMenuModule::where('is_other_module', 0)->whereIn('id', $roleMenuMapIds)->with([
                'children' => function ($query) use ($roleMenuMapIds) {
                    $query->whereIn('id', $roleMenuMapIds);
                }])->where('parent_module_id', null)->get();
            return ['status' => 'success', 'data' => $modules];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function permittedOtherModules($master_designation_id): array
    {
        try {
            $roleMenuMapIds = PMenuModuleRoleMap::where('master_designation_id', $master_designation_id)->pluck('p_menu_module_id');
            $modules = PMenuModule::where('is_other_module', 1)->whereIn('id', $roleMenuMapIds)->with([
                'children' => function ($query) use ($roleMenuMapIds) {
                    $query->whereIn('id', $roleMenuMapIds);
                }])->where('parent_module_id', null)->get();
            return ['status' => 'success', 'data' => $modules];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function permittedModuleWiseMenus($master_designation_id, $module_link): array
    {
        try {
            $module = PMenuModule::where('module_link', $module_link)->first();
            $role = PRole::where('master_designation_id', $master_designation_id)->first();

            $roleMenuMapIds = PMenuRoleMap::where('p_role_id', $role->id)->pluck('p_menu_id');
            $menus = PMenu::where('module_menu_id', $module->id)->whereIn('id', $roleMenuMapIds)->with([
                'children' => function ($query) use ($roleMenuMapIds) {
                    $query->whereIn('id', $roleMenuMapIds);
                }])->where('parent_menu_id', null)->get();
            $data = [
                'module' => $module,
                'menus' => $menus,
            ];
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getMenuModuleLists()
    {
        try {
            $data = PMenuModule::where('parent_module_id', null)->with(['children.menus.children', 'menus.children'])->orderBy('display_order')->get();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function assignModulesToRole(Request $request)
    {
        try {
            $modules = json_decode($request->modules) ?: [];
            $menus = json_decode($request->menus) ?: [];
            $role_id = $request->role_id;
            $menus = PMenu::whereIn('id', $menus)->get();
            $modules = PMenuModule::whereIn('id', $modules)->get();
            $role = PRole::find($role_id);

            $moduleAssign = $role->modules()->sync($modules);
            $menuAssign = $role->menus()->sync($menus);
            return ['status' => 'success', 'data' => 'success'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

}
