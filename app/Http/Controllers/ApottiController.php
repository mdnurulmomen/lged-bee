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

    public function onucchedMerge(Request $request, ApottiService $apottiService){
        $apotti_merge = $apottiService->onucchedMerge($request);
        if (isSuccessResponse($apotti_merge)) {
            $response = responseFormat('success', $apotti_merge['data']);
        } else {
            $response = responseFormat('error', $apotti_merge['data']);
        }

        return response()->json($response);
    }
}
