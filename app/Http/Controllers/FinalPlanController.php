<?php

namespace App\Http\Controllers;

use App\Repository\FinalPlanRepo;
use Illuminate\Http\Request;

class FinalPlanController extends Controller
{

    public function list(Request $request, FinalPlanRepo $oPFinalPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $oPFinalPlanRepo->list($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request, FinalPlanRepo $oPFinalPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $oPFinalPlanRepo->store($request);
            $response = responseFormat('success', 'Successfully Saved.');
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function edit(Request $request, FinalPlanRepo $oPFinalPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $oPFinalPlanRepo->edit($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, FinalPlanRepo $oPFinalPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $oPFinalPlanRepo->update($request);
            $response = responseFormat('success', 'Successfully Updated.');
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function documentIsExist(Request $request, FinalPlanRepo $oPFinalPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $oPFinalPlanRepo->documentIsExist($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


}
