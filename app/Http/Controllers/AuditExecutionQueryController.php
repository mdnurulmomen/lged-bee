<?php

namespace App\Http\Controllers;

use App\Services\AuditExecutionQueryService;
use Illuminate\Http\Request;

class AuditExecutionQueryController extends Controller
{
    public function auditQueryScheduleList(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $auditExecutionQueryService->auditQueryScheduleList($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }

        return response()->json($response);
    }
}
