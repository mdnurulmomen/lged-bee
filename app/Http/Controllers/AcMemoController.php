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

    public function auditMemoList(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $acMemoService->auditMemoList($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }

        return response()->json($response);
    }

    public function auditMemoEdit(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $acMemoService->auditMemoEdit($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }

        return response()->json($response);
    }

    public function auditMemoUpdate(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $acMemoService->auditMemoUpdate($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }

        return response()->json($response);
    }

    public function sendMemoToRpu(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $acMemoService->sendMemoToRpu($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }

        return response()->json($response);
    }

    public function authorityMemoList(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $query_schedule_list = $acMemoService->authorityMemoList($request);
        if (isSuccessResponse($query_schedule_list)) {
            $response = responseFormat('success', $query_schedule_list['data']);
        } else {
            $response = responseFormat('error', $query_schedule_list['data']);
        }

        return response()->json($response);
    }

    public function auditMemoRecommendationStore(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $auditMemoRecommendationStore = $acMemoService->auditMemoRecommendationStore($request);
        if (isSuccessResponse($auditMemoRecommendationStore)) {
            $response = responseFormat('success', $auditMemoRecommendationStore['data']);
        } else {
            $response = responseFormat('error', $auditMemoRecommendationStore['data']);
        }

        return response()->json($response);
    }

    public function auditMemoRecommendationList(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $auditMemoRecommendationList = $acMemoService->auditMemoRecommendationList($request);
        if (isSuccessResponse($auditMemoRecommendationList)) {
            $response = responseFormat('success', $auditMemoRecommendationList['data']);
        } else {
            $response = responseFormat('error', $auditMemoRecommendationList['data']);
        }

        return response()->json($response);
    }

    public function auditMemoLogList(Request $request, AcMemoService $acMemoService): \Illuminate\Http\JsonResponse
    {
        $auditMemoLogList = $acMemoService->auditMemoLogList($request);
        if (isSuccessResponse($auditMemoLogList)) {
            $response = responseFormat('success', $auditMemoLogList['data']);
        } else {
            $response = responseFormat('error', $auditMemoLogList['data']);
        }
        return response()->json($response);
    }
}
