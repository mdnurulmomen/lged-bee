<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\QCService;
use Illuminate\Http\Request;

class QCController extends Controller
{
    public function loadApprovePlanList(Request $request, QCService $qCService): \Illuminate\Http\JsonResponse
    {
        $responseData = $qCService->loadApprovePlanList($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function createNewAirReport(Request $request, QCService $qCService): \Illuminate\Http\JsonResponse
    {
        $responseData = $qCService->createNewAIRReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function editAirReport(Request $request, QCService $qCService): \Illuminate\Http\JsonResponse
    {
        $responseData = $qCService->editAirReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function storeAirReport(Request $request, QCService $qCService): \Illuminate\Http\JsonResponse
    {
        $responseData = $qCService->storeAirReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditTeam(Request $request, QCService $qCService): \Illuminate\Http\JsonResponse
    {
        $responseData = $qCService->getAuditTeam($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditTeamSchedule(Request $request, QCService $qCService): \Illuminate\Http\JsonResponse
    {
        $responseData = $qCService->getAuditTeamSchedule($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditApotti(Request $request, QCService $qCService): \Illuminate\Http\JsonResponse
    {
        $responseData = $qCService->getAuditApotti($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
