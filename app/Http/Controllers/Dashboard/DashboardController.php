<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getTotalDailyQueryAndMemo(Request $request, DashboardService $dashboardService): \Illuminate\Http\JsonResponse
    {
        $getData = $dashboardService->getTotalDailyQueryAndMemo($request);
        if (isSuccessResponse($getData)) {
            $response = responseFormat('success', $getData['data']);
        } else {
            $response = responseFormat('error', $getData['data']);
        }
        return response()->json($response);
    }

    public function getTotalWeeklyQueryAndMemo(Request $request, DashboardService $dashboardService): \Illuminate\Http\JsonResponse
    {
        $getData = $dashboardService->getTotalWeeklyQueryAndMemo($request);
        if (isSuccessResponse($getData)) {
            $response = responseFormat('success', $getData['data']);
        } else {
            $response = responseFormat('error', $getData['data']);
        }
        return response()->json($response);
    }
}
