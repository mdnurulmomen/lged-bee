<?php

namespace App\Http\Controllers;

use App\Services\AuditAssessmentService;
use Illuminate\Http\Request;

class AuditAssessmentController extends Controller
{
    public function store(Request $request, AuditAssessmentService $auditAssessmentService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAssessmentService->store($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function storeAnnualPlan(Request $request, AuditAssessmentService $auditAssessmentService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAssessmentService->storeAnnualPlan($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function list(Request $request, AuditAssessmentService $auditAssessmentService): \Illuminate\Http\JsonResponse
    {
        $responseData = $auditAssessmentService->list($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
