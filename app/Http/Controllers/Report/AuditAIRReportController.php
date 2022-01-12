<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\AuditAIRReportService;
use Illuminate\Http\Request;

class AuditAIRReportController extends Controller
{
    public function loadApprovePlanList(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->loadApprovePlanList($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function createNewAirReport(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->createNewAIRReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function editAirReport(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->editAirReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function storeAirReport(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->storeAirReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditTeam(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditTeam($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditTeamSchedule(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditTeamSchedule($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditApottiList(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditApottiList($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAirWiseAuditApottiList(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAirWiseAuditApottiList($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditApotti(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditApotti($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function storeAirMovement(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->storeAirMovement($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAirLastMovement(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAirLastMovement($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }


    public function getApprovePreliminaryAir(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getApprovePreliminaryAir($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
