<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ObservationReportService;
use Illuminate\Http\Request;

class ObservationReportController extends Controller
{
    public function list(Request $request, ObservationReportService $observationReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $observationReportService->list($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function download(Request $request, ObservationReportService $observationReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $observationReportService->download($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
