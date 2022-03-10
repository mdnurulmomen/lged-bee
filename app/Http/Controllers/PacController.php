<?php

namespace App\Http\Controllers;

use App\Services\PacService;
use Illuminate\Http\Request;

class PacController extends Controller
{
    public function getPacMeetingList(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $meeting_list = $pacService->getPacMeetingList($request);
        if (isSuccessResponse($meeting_list)) {
            $response = responseFormat('success', $meeting_list['data']);
        } else {
            $response = responseFormat('error', $meeting_list['data']);
        }
        return response()->json($response);
    }

    public function pacMeetingStore(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $meeting_list = $pacService->pacMeetingStore($request);
        if (isSuccessResponse($meeting_list)) {
            $response = responseFormat('success', $meeting_list['data']);
        } else {
            $response = responseFormat('error', $meeting_list['data']);
        }
        return response()->json($response);
    }

    public function createPacReport(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->createPacReport($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }
}
