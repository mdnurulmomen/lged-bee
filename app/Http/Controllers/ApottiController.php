<?php

namespace App\Http\Controllers;

use App\Services\ApottiService;
use Illuminate\Http\Request;

class ApottiController extends Controller
{
    public function getApottiList(Request $request, ApottiService $apottiService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $apottiService->getApottiList($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }

        return response()->json($response);
    }

    public function getApottiInfo(Request $request, ApottiService $apottiService): \Illuminate\Http\JsonResponse
    {
        $apotti_info = $apottiService->getApottiInfo($request);
        if (isSuccessResponse($apotti_info)) {
            $response = responseFormat('success', $apotti_info['data']);
        } else {
            $response = responseFormat('error', $apotti_info['data']);
        }

        return response()->json($response);
    }

    public function onucchedMerge(Request $request, ApottiService $apottiService){
        $apotti_merge = $apottiService->onucchedMerge($request);
        if (isSuccessResponse($apotti_merge)) {
            $response = responseFormat('success', $apotti_merge['data']);
        } else {
            $response = responseFormat('error', $apotti_merge['data']);
        }

        return response()->json($response);
    }

    public function onucchedUnMerge(Request $request, ApottiService $apottiService){
        $apotti_merge = $apottiService->onucchedUnMerge($request);
        if (isSuccessResponse($apotti_merge)) {
            $response = responseFormat('success', $apotti_merge['data']);
        } else {
            $response = responseFormat('error', $apotti_merge['data']);
        }

        return response()->json($response);
    }

    public function onucchedReArrange(Request $request, ApottiService $apottiService){
        $apotti_rearrange = $apottiService->onucchedReArrange($request);
        if (isSuccessResponse($apotti_rearrange)) {
            $response = responseFormat('success', $apotti_rearrange['data']);
        } else {
            $response = responseFormat('error', $apotti_rearrange['data']);
        }

        return response()->json($response);
    }
}
