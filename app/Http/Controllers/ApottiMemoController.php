<?php

namespace App\Http\Controllers;

use App\Services\ApottiMemoService;
use Illuminate\Http\Request;

class ApottiMemoController extends Controller
{
    public function memoList(Request $request, ApottiMemoService $memoToApottiService): \Illuminate\Http\JsonResponse
    {
        $responseData = $memoToApottiService->memoList($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function convertMemoToApotti(Request $request, ApottiMemoService $memoToApottiService): \Illuminate\Http\JsonResponse
    {
        $responseData = $memoToApottiService->convertMemoToApotti($request);
        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }
}
