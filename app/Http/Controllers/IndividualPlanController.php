<?php

namespace App\Http\Controllers;

use App\Repository\ApEntityAuditPlanRepository;
use App\Services\IndividualPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IndividualPlanController extends Controller
{
    public function getAllAuditPlans(IndividualPlanService $individualPlanService)
    {
        $plan_info = $individualPlanService->getAllAuditPlans();

        if (isSuccessResponse($plan_info)) {
            $response = responseFormat('success', $plan_info['data']);
        } else {
            $response = responseFormat('error', $plan_info['data']);
        }
        return response()->json($response);
    }

    public function getAllWorkPapers(Request $request, IndividualPlanService $individualPlanService)
    {
        $plan_info = $individualPlanService->getAllWorkPapers($request);

        if (isSuccessResponse($plan_info)) {
            $response = responseFormat('success', $plan_info['data']);
        } else {
            $response = responseFormat('error', $plan_info['data']);
        }
        return response()->json($response);
    }

    public function uploadWorkPapers(Request $request, IndividualPlanService $individualPlanService)
    {
        $store = $riskAssessmentFactorService->uploadWorkPapers($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }

    public function auditPlanInfo(Request $request, IndividualPlanService $individualPlanService): \Illuminate\Http\JsonResponse
    {
        $plan_info = $individualPlanService->auditPlanInfo($request);

        if (isSuccessResponse($plan_info)) {
            $response = responseFormat('success', $plan_info['data']);
        } else {
            $response = responseFormat('error', $plan_info['data']);
        }
        return response()->json($response);
    }

    public function store(Request $request, IndividualPlanService $individualPlanService): \Illuminate\Http\JsonResponse
    {
        $add_plan = $individualPlanService->store($request);

        if (isSuccessResponse($add_plan)) {
            $response = responseFormat('success', $add_plan['data']);
        } else {
            $response = responseFormat('error', $add_plan['data']);
        }
        return response()->json($response);
    }
}
