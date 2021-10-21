<?php

namespace App\Http\Controllers;

use App\Services\AcMemoService;
use Illuminate\Http\Request;

class AcMemoController extends Controller
{
    public function auditMemoStore(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $acMemoService->auditMemoStore($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }

        return response()->json($response);
    }
}
