<?php

namespace App\Http\Controllers;

use App\Models\PMenu;
use App\Models\PMenuModule;
use App\Models\PMenuModuleRoleMap;
use App\Models\PMenuRoleMap;
use App\Models\PRole;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function modules(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $roleMenuMapIds = PMenuModuleRoleMap::where('role_id', $cdesk->master_designation_id)->pluck('module_id');
        return PMenuModule::whereIn('id', $roleMenuMapIds)->with([
            'children' => function ($query) use ($roleMenuMapIds) {
                $query->whereIn('id', $roleMenuMapIds);
            }])->where('parent_menu_id', '0')->get();
    }

    public function otherModules(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $roleMenuMapIds = PMenuModuleRoleMap::where('role_id', $cdesk->master_designation_id)->pluck('module_id');
        return PMenuModule::whereIn('id', $roleMenuMapIds)->with([
            'children' => function ($query) use ($roleMenuMapIds) {
                $query->whereIn('id', $roleMenuMapIds);
            }])->where('parent_menu_id', '0')->get();
    }

    public function menus(Request $request)
    {
        $roleMenuMapIds = PMenuRoleMap::where('role_id', 1)->pluck('menu_id');
        return PMenu::whereIn('id', $roleMenuMapIds)->with([
            'children' => function ($query) use ($roleMenuMapIds) {
                $query->whereIn('id', $roleMenuMapIds);
            }])->where('parent_menu_id', '0')->get();
    }

    public function assignModulesToRole(Request $request)
    {

        $assignedMenus = $request->input('modules') ?: [];
        $role_id = $request->input('role_id');
        $menus = PMenuModule::whereIn('id', $assignedMenus)->get();
        $role = PRole::find($role_id);

        if ($role->modules()->sync($menus))
            return response()->json(['msg' => 'মেনু প্রদান করা হয়েছে।', 'status' => 'success'], 200);
        else
            return response()->json(['msg' => 'Error', 'status' => 'error'], 500);
    }
}
