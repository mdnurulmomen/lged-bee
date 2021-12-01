<?php

namespace App\Http\Controllers;

use App\Services\ApRiskAssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApRiskAssessmentController extends Controller
{
    public function store(Request $request, ApRiskAssessmentService $apRiskAssessmentService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'risk_assessments' => 'required',
            'fiscal_year_id' => 'required',
            'activity_id' => 'required',
            'audit_plan_id' => 'required',
            'risk_rate' => 'required',
            'risk' => 'required',
        ])->validate();

        $responseData = $apRiskAssessmentService->store($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function update(Request $request, ApRiskAssessmentService $apRiskAssessmentService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'id' => 'required',
            'risk_assessments' => 'required',
            'fiscal_year_id' => 'required',
            'activity_id' => 'required',
            'audit_plan_id' => 'required',
            'risk_rate' => 'required',
            'risk' => 'required',
        ])->validate();

        $responseData = $apRiskAssessmentService->update($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function apRiskAssessmentList(Request $request, ApRiskAssessmentService $apRiskAssessmentService): \Illuminate\Http\JsonResponse
    {
        $responseData = $apRiskAssessmentService->apRiskAssessmentList($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function riskAssessmentTypeWiseItemList(Request $request, ApRiskAssessmentService $apRiskAssessmentService): \Illuminate\Http\JsonResponse
    {
        $responseData = $apRiskAssessmentService->riskAssessmentTypeWiseItemList($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
