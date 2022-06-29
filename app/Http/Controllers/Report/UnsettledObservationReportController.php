<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\UnsettledObservationReportService;
use Illuminate\Http\Request;

class UnsettledObservationReportController extends Controller
{
    public function list(Request $request, UnsettledObservationReportService $unsettledObservationReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $unsettledObservationReportService->list($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function download(Request $request, UnsettledObservationReportService $unsettledObservationReportService): \Illuminate\Http\JsonResponse
    {
        $responseData = $unsettledObservationReportService->download($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
