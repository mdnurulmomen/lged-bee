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

    public function sendAuditQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $auditExecutionQueryService->sendAuditQuery($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }
        return response()->json($response);
    }

    public function receivedAuditQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $auditExecutionQueryService->receivedAuditQuery($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }
        return response()->json($response);
    }

    public function auditQueryCostCenterTypeWise(Request $request, AuditExecutionQueryService $auditExecutionQueryService)
    {
        $cost_center_wise_query = $auditExecutionQueryService->auditQueryCostCenterTypeWise($request);
        if (isSuccessResponse($cost_center_wise_query)) {
            $response = responseFormat('success', $cost_center_wise_query['data']);
        } else {
            $response = responseFormat('error', $cost_center_wise_query['data']);
        }
        return response()->json($response);
    }
}
