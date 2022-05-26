<?php

namespace App\Http\Controllers;

use App\Services\ArchiveApottiReportService;
use Illuminate\Http\Request;

class ArchiveApottiReportController extends Controller
{
    public function store(Request $request, ArchiveApottiReportService $archiveApottiReportService): \Illuminate\Http\JsonResponse
    {
        $storeApotti = $archiveApottiReportService->store($request);
        if (isSuccessResponse($storeApotti)) {
            $response = responseFormat('success', $storeApotti['data']);
        } else {
            $response = responseFormat('error', $storeApotti['data']);
        }
        return response()->json($response);
    }

    public function list(Request $request, ArchiveApottiReportService $archiveApottiReportService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $archiveApottiReportService->list($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }
        return response()->json($response);
    }

    public function view(Request $request, ArchiveApottiReportService $archiveApottiReportService): \Illuminate\Http\JsonResponse
    {
        $apotti = $archiveApottiReportService->view($request);
        if (isSuccessResponse($apotti)) {
            $response = responseFormat('success', $apotti['data']);
        } else {
            $response = responseFormat('error', $apotti['data']);
        }
        return response()->json($response);
    }

    public function storeReportApotii(Request $request, ArchiveApottiReportService $archiveApottiReportService): \Illuminate\Http\JsonResponse
    {
        $apotti = $archiveApottiReportService->storeReportApotii($request);
        if (isSuccessResponse($apotti)) {
            $response = responseFormat('success', $apotti['data']);
        } else {
            $response = responseFormat('error', $apotti['data']);
        }
        return response()->json($response);
    }

    public function reportSync(Request $request, ArchiveApottiReportService $archiveApottiReportService): \Illuminate\Http\JsonResponse
    {
        $apotti = $archiveApottiReportService->reportSync($request);
        if (isSuccessResponse($apotti)) {
            $response = responseFormat('success', $apotti['data']);
        } else {
            $response = responseFormat('error', $apotti['data']);
        }
        return response()->json($response);
    }

}
