<?php

namespace App\Http\Controllers;

use App\Services\XRiskFactorService;
use Illuminate\Http\Request;

class XRiskFactorController extends Controller
{
    public function list(Request $request, XRiskFactorService $XRiskFactorService)
    {
        $list = $XRiskFactorService->list($request);

        if (isSuccessResponse($list)) {
            $response = responseFormat('success', $list['data']);
        } else {
            $response = responseFormat('error', $list['data']);
        }

        return response()->json($response);
    }

    public function store(Request $request, XRiskFactorService $XRiskFactorService)
    {
        $store = $XRiskFactorService->store($request);
        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }

        return response()->json($response);
    }
}
