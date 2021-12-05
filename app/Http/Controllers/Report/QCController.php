<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\QCService;
use Illuminate\Http\Request;

class QCController extends Controller
{
    public function createNewAirReport(Request $request, QCService $qCService): \Illuminate\Http\JsonResponse
    {
        $responseData = $qCService->createNewAIRReport($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
