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

    public function getPacMeetingInfo(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $meeting_list = $pacService->getPacMeetingInfo($request);
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

    public function createPacWorksheetReport(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->createPacWorksheetReport($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }

    public function storePacWorksheetReport(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->storePacWorksheetReport($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }

    public function getPACDashboardData(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->getPACDashboardData($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }


    public function getPACFinalReport(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->getPACFinalReport($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }


    public function showPACFinalReport(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->showPACFinalReport($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }

    public function getPACApottiList(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->getPACApottiList($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }

    public function showPACApotti(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->showPACApotti($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }

    public function pacMeetingApottiDecisionStore(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->pacMeetingApottiDecisionStore($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }

    public function sentToPac(Request $request, PacService $pacService): \Illuminate\Http\JsonResponse
    {
        $responseData = $pacService->sentToPac($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }
}
