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

    public function getBroadSheetInfo(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $broadsheetReplyService->getBroadSheetInfo($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }
        return response()->json($response);
    }



    public function getBroadSheetItems(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_info = $broadsheetReplyService->getBroadSheetItems($request);
        if (isSuccessResponse($apotti_info)) {
            $response = responseFormat('success', $apotti_info['data']);
        } else {
            $response = responseFormat('error', $apotti_info['data']);
        }

        return response()->json($response);
    }

    public function getBroadSheetItemInfo(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_info = $broadsheetReplyService->getBroadSheetItemInfo($request);
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

    public function approveBroadSheetItem(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_info = $broadsheetReplyService->approveBroadSheetItem($request);
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

    public function storeBroadSheetReply(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $responseData = $broadsheetReplyService->storeBroadSheetReply($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function sendBroadSheetReplyToRpu(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $responseData = $broadsheetReplyService->sendBroadSheetReplyToRpu($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getSentBroadSheetInfo(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $broadsheetReplyService->getSentBroadSheetInfo($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }
        return response()->json($response);
    }

    public function getAllBroadSheetMinistry(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $ministry_list = $broadsheetReplyService->getAllBroadSheetMinistry($request);
        if (isSuccessResponse($ministry_list)) {
            $response = responseFormat('success', $ministry_list['data']);
        } else {
            $response = responseFormat('error', $ministry_list['data']);
        }
        return response()->json($response);
    }

    public function getAllBroadSheetMinistryWiseEntity(Request $request, BroadsheetReplyService $broadsheetReplyService): \Illuminate\Http\JsonResponse
    {
        $entity_list = $broadsheetReplyService->getAllBroadSheetMinistryWiseEntity($request);
        if (isSuccessResponse($entity_list)) {
            $response = responseFormat('success', $entity_list['data']);
        } else {
            $response = responseFormat('error', $entity_list['data']);
        }
        return response()->json($response);
    }

}
