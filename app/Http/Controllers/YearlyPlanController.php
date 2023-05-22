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
    public function update(Request $request, YearlyPlanService $yearlyPlanService)
    {
        $update = $yearlyPlanService->update($request);
        if (isSuccessResponse($update)) {
            $response = responseFormat('success', $update['data']);
        } else {
            $response = responseFormat('error', $update['data']);
        }

        return response()->json($response);
    }
    public function deleteYearlyPlan(Request $request, YearlyPlanService $yearlyPlanService)
    {
        $deleteYearlyPlan = $yearlyPlanService->deleteYearlyPlan($request);
        if (isSuccessResponse($deleteYearlyPlan)) {
            $response = responseFormat('success', $deleteYearlyPlan['data']);
        } else {
            $response = responseFormat('error', $deleteYearlyPlan['data']);
        }

        return response()->json($response);
    }

    public function yearlyPlanLocationDelete(Request $request, YearlyPlanService $yearlyPlanService)
    {
        $yearlyPlanLocationDelete = $yearlyPlanService->yearlyPlanLocationDelete($request);
        if (isSuccessResponse($yearlyPlanLocationDelete)) {
            $response = responseFormat('success', $yearlyPlanLocationDelete['data']);
        } else {
            $response = responseFormat('error', $yearlyPlanLocationDelete['data']);
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
