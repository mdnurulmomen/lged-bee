<?php

namespace App\Http\Controllers;

use App\Services\YearlyPlanService;
use Illuminate\Http\Request;

class YearlyPlanController extends Controller
{
    public function list(Request $request, YearlyPlanService $yearlyPlanService)
    {
        $list = $yearlyPlanService->list($request);

        if (isSuccessResponse($list)) {
            $response = responseFormat('success', $list['data']);
        } else {
            $response = responseFormat('error', $list['data']);
        }

        return response()->json($response);
    }

    public function store(Request $request, YearlyPlanService $yearlyPlanService)
    {
        $store = $yearlyPlanService->store($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }

    public function getIndividualYearlyPlan(Request $request, YearlyPlanService $yearlyPlanService)
    {
        $store = $yearlyPlanService->getIndividualYearlyPlan($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }

    public function getIndividualYearlyPlanYear(Request $request, YearlyPlanService $yearlyPlanService)
    {
        $store = $yearlyPlanService->getIndividualYearlyPlanYear($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }
}
