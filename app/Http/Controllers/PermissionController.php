<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function modules(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
    {
        $modules = $permissionService->permittedModules($request);
        if (isSuccessResponse($modules)) {
            $response = responseFormat('success', $modules['data']);
        } else {
            $response = responseFormat('error', $modules['data']);
        }

        return response()->json($response);
    }

    public function otherModules(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
    {
        $modules = $permissionService->permittedOtherModules($request);
        if (isSuccessResponse($modules)) {
            $response = responseFormat('success', $modules['data']);
        } else {
            $response = responseFormat('error', $modules['data']);
        }

        return response()->json($response);
    }

    public function menus(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
    {
        $menus = $permissionService->permittedModuleWiseMenus($request);
        if (isSuccessResponse($menus)) {
            $response = responseFormat('success', $menus['data']);
        } else {
            $response = responseFormat('error', $menus['data']);
        }

        return response()->json($response);
    }

    public function getAllMenuActionLists(Request $request, PermissionService $permissionService)
    {
        $menus = $permissionService->getAllMenuActionLists($request);
        if (isSuccessResponse($menus)) {
            $response = responseFormat('success', $menus['data']);
        } else {
            $response = responseFormat('error', $menus['data']);
        }
        return response()->json($response);
    }

    public function getAllMenuActionsRoleWise(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
    {
        $menus = $permissionService->getAllMenuActionsRoleWise($request);
        if (isSuccessResponse($menus)) {
            $response = responseFormat('success', $menus['data']);
        } else {
            $response = responseFormat('error', $menus['data']);
        }
        return response()->json($response);
    }

    public function assignMenuActionsToRole(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
    {
        $assign = $permissionService->assignMenuActionsToRole($request);
        if (isSuccessResponse($assign)) {
            $response = responseFormat('success', $assign['data']);
        } else {
            $response = responseFormat('error', $assign['data']);
        }
        return response()->json($response);
    }

    public function assignMenuActionsToEmployee(Request $request, PermissionService $permissionService): \Illuminate\Http\JsonResponse
    {
        $assign = $permissionService->assignIndividualMenuActionsToEmployee($request);
        if (isSuccessResponse($assign)) {
            $response = responseFormat('success', $assign['data']);
        } else {
            $response = responseFormat('error', $assign['data']);
        }
        return response()->json($response);
    }
}
