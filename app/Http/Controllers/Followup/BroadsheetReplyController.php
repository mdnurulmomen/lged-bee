<?php

namespace App\Http\Controllers\Followup;

use App\Http\Controllers\Controller;
use App\Services\BroadsheetReplyService;
use Illuminate\Http\Request;

class BroadsheetReplyController extends Controller
{
    public function getApottiItemList(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $broadsheetReplyService->getApottiItemList($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }
        return response()->json($response);
    }

    public function getApottiItemInfo(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_info = $broadsheetReplyService->getApottiItemInfo($request);
        if (isSuccessResponse($apotti_info)) {
            $response = responseFormat('success', $apotti_info['data']);
        } else {
            $response = responseFormat('error', $apotti_info['data']);
        }

        return response()->json($response);
    }

}
