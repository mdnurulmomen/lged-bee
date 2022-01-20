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

    public function loadAuditQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService)
    {
        $auditQueries = $auditExecutionQueryService->loadAuditQuery($request);
        if (isSuccessResponse($auditQueries)) {
            $response = responseFormat('success', $auditQueries['data']);
        } else {
            $response = responseFormat('error', $auditQueries['data']);
        }
        return response()->json($response);
    }

    public function loadTypeWiseAuditQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService)
    {
        $auditQueries = $auditExecutionQueryService->loadTypeWiseAuditQuery($request);
        if (isSuccessResponse($auditQueries)) {
            $response = responseFormat('success', $auditQueries['data']);
        } else {
            $response = responseFormat('error', $auditQueries['data']);
        }
        return response()->json($response);
    }

    public function rejectedAuditQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $auditExecutionQueryService->rejectedAuditQuery($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }
        return response()->json($response);
    }

    public function rpuSendQueryList(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $ac_query_list = $auditExecutionQueryService->rpuSendQueryList($request);
        if (isSuccessResponse($ac_query_list)) {
            $response = responseFormat('success', $ac_query_list['data']);
        } else {
            $response = responseFormat('error', $ac_query_list['data']);
        }
        return response()->json($response);
    }

    public function storeAuditQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $ac_query_list = $auditExecutionQueryService->storeAuditQuery($request);
        if (isSuccessResponse($ac_query_list)) {
            $response = responseFormat('success', $ac_query_list['data']);
        } else {
            $response = responseFormat('error', $ac_query_list['data']);
        }
        return response()->json($response);
    }

    public function viewAuditQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $ac_query_list = $auditExecutionQueryService->viewAuditQuery($request);
        if (isSuccessResponse($ac_query_list)) {
            $response = responseFormat('success', $ac_query_list['data']);
        } else {
            $response = responseFormat('error', $ac_query_list['data']);
        }
        return response()->json($response);
    }

    public function updateAuditQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $ac_query_list = $auditExecutionQueryService->updateAuditQuery($request);
        if (isSuccessResponse($ac_query_list)) {
            $response = responseFormat('success', $ac_query_list['data']);
        } else {
            $response = responseFormat('error', $ac_query_list['data']);
        }
        return response()->json($response);
    }

    public function authorityQueryList(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $auditExecutionQueryService->authorityQueryList($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }

        return response()->json($response);
    }

    public function responseOfRpuQuery(Request $request, AuditExecutionQueryService $auditExecutionQueryService): \Illuminate\Http\JsonResponse
    {
        $auditMemoRecommendationStore = $auditExecutionQueryService->responseOfRpuQuery($request);
        if (isSuccessResponse($auditMemoRecommendationStore)) {
            $response = responseFormat('success', $auditMemoRecommendationStore['data']);
        } else {
            $response = responseFormat('error', $auditMemoRecommendationStore['data']);
        }

        return response()->json($response);
    }
}
