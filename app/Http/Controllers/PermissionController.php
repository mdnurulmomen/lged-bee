<?php

namespace App\Http\Controllers;

use App\Models\PMenuModule;
use App\Models\PRole;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function modules(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
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

    public function otherModules(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
    {
        $cdesk = json_decode($request->cdesk, false);
        $modules = $permissionService->permittedOtherModules($cdesk->master_desigation_id);
        if (isSuccessResponse($modules)) {
            $response = responseFormat('success', $modules['data']);
        } else {
            $response = responseFormat('error', $modules['data']);
        }

        return response()->json($response);
    }

    public function menus(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
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

    public function getMenuModuleLists(Request $request, PermissionService $permissionService)
    {
        $cdesk = json_decode($request->cdesk, false);
        $menus = $permissionService->getMenuModuleLists();
        if (isSuccessResponse($menus)) {
            $response = responseFormat('success', $menus['data']);
        } else {
            $response = responseFormat('error', $menus['data']);
        }
        return response()->json($response);
    }

    public function assignModulesToRole(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
    {
        $assign = $permissionService->assignModulesToRole($request);
        if (isSuccessResponse($assign)) {
            $response = responseFormat('success', $assign['data']);
        } else {
            $response = responseFormat('error', $assign['data']);
        }
        return response()->json($response);
    }
}
