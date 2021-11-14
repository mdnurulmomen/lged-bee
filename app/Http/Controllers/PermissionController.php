<?php

namespace App\Http\Controllers;

use App\Models\PMenuModule;
use App\Models\PRole;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function modules(Request $request, PermissionService $permissionService)
    {
        $cdesk = json_decode($request->cdesk, false);
        $modules = $permissionService->permittedModules($cdesk->master_desigation_id);
        if (isSuccessResponse($modules)) {
            $response = responseFormat('success', $modules['data']);
        } else {
            $response = responseFormat('error', $modules['data']);
        }

        return response()->json($response);
    }

    public function otherModules(Request $request, PermissionService $permissionService)
    {
        $cdesk = json_decode($request->cdesk, false);
        $modules = $permissionService->permittedModules($cdesk->master_desigation_id);
        if (isSuccessResponse($modules)) {
            $response = responseFormat('success', $modules['data']);
        } else {
            $response = responseFormat('error', $modules['data']);
        }

        return response()->json($response);
    }

    public function menus(Request $request, PermissionService $permissionService)
    {
        $cdesk = json_decode($request->cdesk, false);
        $menus = $permissionService->permittedModuleWiseMenus($cdesk->master_designation_id, $request->module_link);
        if (isSuccessResponse($menus)) {
            $response = responseFormat('success', $menus['data']);
        } else {
            $response = responseFormat('error', $menus['data']);
        }

        return response()->json($response);
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
