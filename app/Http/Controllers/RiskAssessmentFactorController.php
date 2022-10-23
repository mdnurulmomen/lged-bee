<?php

namespace App\Http\Controllers;

use App\Services\RiskAssessmentFactorService;
use App\Services\StrategicPlanService;
use Illuminate\Http\Request;

class RiskAssessmentFactorController extends Controller
{
    public function list(Request $request, RiskAssessmentFactorService $riskAssessmentFactorService)
    {
        $list = $riskAssessmentFactorService->list($request);

        if (isSuccessResponse($list)) {
            $response = responseFormat('success', $list['data']);
        } else {
            $response = responseFormat('error', $list['data']);
        }

        return response()->json($response);
    }

    public function store(Request $request, RiskAssessmentFactorService $riskAssessmentFactorService)
    {
        $store = $riskAssessmentFactorService->store($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }
}
