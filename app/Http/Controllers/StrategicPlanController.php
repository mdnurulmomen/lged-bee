<?php

namespace App\Http\Controllers;

use App\Services\StrategicPlanService;
use Illuminate\Http\Request;

class StrategicPlanController extends Controller
{
    public function list(Request $request, StrategicPlanService $strategicPlanService)
    {
        $list = $strategicPlanService->list($request);

        if (isSuccessResponse($list)) {
            $response = responseFormat('success', $list['data']);
        } else {
            $response = responseFormat('error', $list['data']);
        }

        return response()->json($response);
    }
    public function delete(Request $request, StrategicPlanService $strategicPlanService)
    {
        $delete = $strategicPlanService->delete($request);

        if (isSuccessResponse($delete)) {
            $response = responseFormat('success', $delete['data']);
        } else {
            $response = responseFormat('error', $delete['data']);
        }

        return response()->json($response);
    }

    public function store(Request $request, StrategicPlanService $strategicPlanService)
    {
        $store = $strategicPlanService->store($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }
    public function update(Request $request, StrategicPlanService $strategicPlanService)
    {
        $update = $strategicPlanService->update($request);
        if (isSuccessResponse($update)) {
            $response = responseFormat('success', $update['data']);
        } else {
            $response = responseFormat('error', $update['data']);
        }

        return response()->json($response);
    }
    public function deleteLocation(Request $request, StrategicPlanService $strategicPlanService)
    {
        $deleteLocation = $strategicPlanService->deleteLocation($request);
        if (isSuccessResponse($deleteLocation)) {
            $response = responseFormat('success', $deleteLocation['data']);
        } else {
            $response = responseFormat('error', $deleteLocation['data']);
        }

        return response()->json($response);
    }

    public function getIndividualStrategicPlan(Request $request, StrategicPlanService $strategicPlanService)
    {
        $store = $strategicPlanService->getIndividualStrategicPlan($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }

    public function getIndividualStrategicPlanYear(Request $request, StrategicPlanService $strategicPlanService)
    {
        $store = $strategicPlanService->getIndividualStrategicPlanYear($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }
}
