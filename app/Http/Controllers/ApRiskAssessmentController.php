<?php

namespace App\Http\Controllers;

use App\Services\ApRiskAssessmentService;
use Illuminate\Http\Request;

class ApRiskAssessmentController extends Controller
{
    public function store(Request $request, ApRiskAssessmentService $apRiskAssessmentService): \Illuminate\Http\JsonResponse
    {
        $store = $apRiskAssessmentService->store($request);

        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }

    public function update(Request $request, ApRiskAssessmentService $apRiskAssessmentService): \Illuminate\Http\JsonResponse
    {
        $store = $apRiskAssessmentService->update($request);

        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }

    public function apRiskAssessmentList(Request $request, ApRiskAssessmentService $apRiskAssessmentService): \Illuminate\Http\JsonResponse
    {
        $store = $apRiskAssessmentService->apRiskAssessmentList($request);

        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }
}
