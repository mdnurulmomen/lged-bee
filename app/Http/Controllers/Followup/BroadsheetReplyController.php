<?php

namespace App\Http\Controllers\Followup;

use App\Http\Controllers\Controller;
use App\Services\BroadsheetReplyService;
use Illuminate\Http\Request;

class BroadsheetReplyController extends Controller
{
    public function getBroadSheetList(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $broadsheetReplyService->getBroadSheetList($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }
        return response()->json($response);
    }



    public function getBroadSheetItem(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_info = $broadsheetReplyService->getBroadSheetItem($request);
        if (isSuccessResponse($apotti_info)) {
            $response = responseFormat('success', $apotti_info['data']);
        } else {
            $response = responseFormat('error', $apotti_info['data']);
        }

        return response()->json($response);
    }

    public function updateBroadSheetItem(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_info = $broadsheetReplyService->updateBroadSheetItem($request);
        if (isSuccessResponse($apotti_info)) {
            $response = responseFormat('success', $apotti_info['data']);
        } else {
            $response = responseFormat('error', $apotti_info['data']);
        }

        return response()->json($response);
    }

    public function broadSheetMovement(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $responseData = $broadsheetReplyService->broadSheetMovement($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function broadSheetLastMovement(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $responseData = $broadsheetReplyService->broadSheetLastMovement($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

}
