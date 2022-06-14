<?php

namespace App\Http\Controllers;

use App\Services\ApottiSearchService;
use Illuminate\Http\Request;
use Whoops\Run;

class ApottiSearchController extends Controller
{
    public function list(Request $request, ApottiSearchService $apottiSearchService): \Illuminate\Http\JsonResponse
    {
        $apotti_list = $apottiSearchService->list($request);
        if (isSuccessResponse($apotti_list)) {
            $response = responseFormat('success', $apotti_list['data']);
        } else {
            $response = responseFormat('error', $apotti_list['data']);
        }
        return response()->json($response);
    }

    public function view(Request $request, ApottiSearchService $apottiSearchService): \Illuminate\Http\JsonResponse
    {
        $apotti = $apottiSearchService->view($request);
        if (isSuccessResponse($apotti)) {
            $response = responseFormat('success', $apotti['data']);
        } else {
            $response = responseFormat('error', $apotti['data']);
        }
        return response()->json($response);
    }
}
