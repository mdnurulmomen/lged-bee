<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getTotalQueryAndMemoReport(Request $request, DashboardService $dashboardService): \Illuminate\Http\JsonResponse
    {
        $getData = $dashboardService->getTotalQueryAndMemoReport($request);
        if (isSuccessResponse($getData)) {
            $response = responseFormat('success', $getData['data']);
        } else {
            $response = responseFormat('error', $getData['data']);
        }
        return response()->json($response);
    }
}
