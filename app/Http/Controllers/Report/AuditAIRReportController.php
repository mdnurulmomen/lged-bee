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

    public function updateQACAirReport(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->updateQACAirReport($request);

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

    public function getAirWiseContentKey(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAirWiseContentKey($request);

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

    public function getAirWiseQACApotti(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAirWiseQACApotti($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAirAndApottiTypeWiseQACApotti(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAirAndApottiTypeWiseQACApotti($request);

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

    public function getAuditApottiWisePrisistos(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditApottiWisePrisistos($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAirWisePorisistos(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAirWisePorisistos($request);

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


    public function getAuditPlanAndTypeWiseAir(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditPlanAndTypeWiseAir($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditFinalReport(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditFinalReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditFinalReportSearch(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditFinalReportSearch($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuditFinalReportDetails(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuditFinalReportDetails($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function deleteAirReportWiseApotti(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->deleteAirReportWiseApotti($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function apottiFinalApproval(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->apottiFinalApproval($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function finalReportMovement(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->finalReportMovement($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function getAuthorityAirReport(Request $request, AuditAIRReportService $auditAIRReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAIRReportService->getAuthorityAirReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
