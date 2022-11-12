<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\XRiskFactorService;

class XRiskFactorController extends Controller
{
    protected $XRiskFactorService;

    public function __construct(XRiskFactorService $XRiskFactorService)
    {
        $this->XRiskFactorService = $XRiskFactorService;
    }

    public function index()
    {
        $list = $this->XRiskFactorService->list();

        if (isSuccessResponse($list)) {
            $response = responseFormat('success', $list['data']);
        } else {
            $response = responseFormat('error', $list['data']);
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        $store = $this->XRiskFactorService->store($request);

        if (isSuccessResponse($store)) {
            $response = responseFormat('success', $store['data']);
        } else {
            $response = responseFormat('error', $store['data']);
        }
        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $update = $this->XRiskFactorService->update($request, $id);

        if (isSuccessResponse($update)) {
            $response = responseFormat('success', $update['data']);
        } else {
            $response = responseFormat('error', $update['data']);
        }
        return response()->json($response);
    }

    public function delete($id)
    {
        $delete = $this->XRiskFactorService->delete($id);

        if (isSuccessResponse($delete)) {
            $response = responseFormat('success', $delete['data']);
        } else {
            $response = responseFormat('error', $delete['data']);
        }
        return response()->json($response);
    }

}
