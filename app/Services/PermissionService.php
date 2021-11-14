<?php

namespace App\Services;

use App\Models\PMenu;
use App\Models\PMenuModule;
use App\Models\PMenuModuleRoleMap;
use App\Models\PMenuRoleMap;
use App\Traits\ApiHeart;
use App\Traits\GenericData;

class PermissionService
{
    use GenericData, ApiHeart;

    public function permittedModules($master_designation_id): array
    {
        try {
            $roleMenuMapIds = PMenuModuleRoleMap::where('role_id', $master_designation_id)->pluck('module_id');
            $modules = PMenuModule::where('is_other_module', 0)->whereIn('id', $roleMenuMapIds)->with([
                'children' => function ($query) use ($roleMenuMapIds) {
                    $query->whereIn('id', $roleMenuMapIds);
                }])->where('parent_menu_id', '0')->get();
            return ['status' => 'success', 'data' => $modules];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function permittedOtherModules($master_designation_id): array
    {
        try {
            $roleMenuMapIds = PMenuModuleRoleMap::where('role_id', $master_designation_id)->pluck('module_id');
            $modules = PMenuModule::where('is_other_module', 1)->whereIn('id', $roleMenuMapIds)->with([
                'children' => function ($query) use ($roleMenuMapIds) {
                    $query->whereIn('id', $roleMenuMapIds);
                }])->where('parent_menu_id', '0')->get();
            return ['status' => 'success', 'data' => $modules];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function permittedModuleWiseMenus($master_designation_id, $module_link): array
    {
        try {
            $module = PMenuModule::where('link', $module_link)->first();
            $roleMenuMapIds = PMenuRoleMap::where('role_id', $master_designation_id)->pluck('menu_id');
            $menus = PMenu::where('menu_module_id', $module->id)->whereIn('id', $roleMenuMapIds)->with([
                'children' => function ($query) use ($roleMenuMapIds) {
                    $query->whereIn('id', $roleMenuMapIds);
                }])->where('parent_menu_id', '0')->get();
            return ['status' => 'success', 'data' => $menus];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

}
