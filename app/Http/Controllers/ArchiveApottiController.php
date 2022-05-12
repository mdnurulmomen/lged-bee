<?php

namespace App\Http\Controllers;

use App\Services\ArchiveApottiService;
use Illuminate\Http\Request;

class ArchiveApottiController extends Controller
{
    public function getOniyomerCategoryList(Request $request, ArchiveApottiService $archiveApottiService): \Illuminate\Http\JsonResponse
    {
        $categories = $archiveApottiService->getOniyomerCategoryList();
        if (isSuccessResponse($categories)) {
            $response = responseFormat('success', $categories['data']);
        } else {
            $response = responseFormat('error', $categories['data']);
        }

        return response()->json($response);
    }

    public function getParentWiseOniyomerCategory(Request $request, ArchiveApottiService $archiveApottiService): \Illuminate\Http\JsonResponse
    {
        $categories = $archiveApottiService->getParentWiseOniyomerCategory($request);
        if (isSuccessResponse($categories)) {
            $response = responseFormat('success', $categories['data']);
        } else {
            $response = responseFormat('error', $categories['data']);
        }

        return response()->json($response);
    }

    public function store(Request $request, ArchiveApottiService $archiveApottiService): \Illuminate\Http\JsonResponse
    {
        $storeApotti = $archiveApottiService->store($request);
        if (isSuccessResponse($storeApotti)) {
            $response = responseFormat('success', $storeApotti['data']);
        } else {
            $response = responseFormat('error', $storeApotti['data']);
        }
        return response()->json($response);
    }

    public function update(Request $request, ArchiveApottiService $archiveApottiService): \Illuminate\Http\JsonResponse
    {
        $updateApotti = $archiveApottiService->update($request);
        if (isSuccessResponse($updateApotti)) {
            $response = responseFormat('success', $updateApotti['data']);
        } else {
            $response = responseFormat('error', $updateApotti['data']);
        }
        return response()->json($response);
    }

    public function list(Request $request, ArchiveApottiService $archiveApottiService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $archiveApottiService->list($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }
        return response()->json($response);
    }

    public function edit(Request $request, ArchiveApottiService $archiveApottiService): \Illuminate\Http\JsonResponse
    {
        $apotti = $archiveApottiService->edit($request);
        if (isSuccessResponse($apotti)) {
            $response = responseFormat('success', $apotti['data']);
        } else {
            $response = responseFormat('error', $apotti['data']);
        }
        return response()->json($response);
    }

}
