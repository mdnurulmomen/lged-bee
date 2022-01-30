<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\RpuAirReportService;
use Illuminate\Http\Request;

class RpuAirReportController extends Controller
{
    public function airSendToRpu(Request $request, RpuAirReportService $rpuAirReportServices): \Illuminate\Http\JsonResponse
    {
        $responseData = $rpuAirReportServices->airSendToRpu($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function receivedAirByRpu(Request $request, RpuAirReportService $rpuAirReportServices): \Illuminate\Http\JsonResponse
    {
        $responseData = $rpuAirReportServices->receivedAirByRpu($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function apottiItemResponseByRpu(Request $request, RpuAirReportService $rpuAirReportServices): \Illuminate\Http\JsonResponse
    {
        $responseData = $rpuAirReportServices->apottiItemResponseByRpu($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

}
