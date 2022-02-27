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

    public function apottiWiseAllItem(Request $request, ApottiService $apottiService){
        $apotti_wise_item = $apottiService->apottiWiseAllItem($request);
        if (isSuccessResponse($apotti_wise_item)) {
            $response = responseFormat('success', $apotti_wise_item['data']);
        } else {
            $response = responseFormat('error', $apotti_wise_item['data']);
        }

        return response()->json($response);
    }

    public function getApottiItemInfo(Request $request, ApottiService $apottiService){
        $apotti_item_info = $apottiService->getApottiItemInfo($request);
        if (isSuccessResponse($apotti_item_info)) {
            $response = responseFormat('success', $apotti_item_info['data']);
        } else {
            $response = responseFormat('error', $apotti_item_info['data']);
        }

        return response()->json($response);
    }

    public function updateApotti(Request $request, ApottiService $apottiService){
        $update_apotti = $apottiService->updateApotti($request);
        if (isSuccessResponse($update_apotti)) {
            $response = responseFormat('success', $update_apotti['data']);
        } else {
            $response = responseFormat('error', $update_apotti['data']);
        }
        return response()->json($response);
    }

    public function getApottiOnucchedNo(Request $request, ApottiService $apottiService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $apottiService->getApottiOnucchedNo($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }

        return response()->json($response);
    }

    public function getApottiRegisterlist(Request $request, ApottiService $apottiService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $apottiService->getApottiRegisterlist($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }

        return response()->json($response);
    }

}
