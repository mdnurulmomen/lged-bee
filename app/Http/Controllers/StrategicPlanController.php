<?php

namespace App\Http\Controllers;

use App\Repository\StrategicPlanRepo;
use Illuminate\Http\Request;

class StrategicPlanController extends Controller
{

    public function store(Request $request, StrategicPlanRepo $strategicPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $strategicPlanRepo->store($request);
            $response = responseFormat('success', 'Successfully Saved.');
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request, StrategicPlanRepo $strategicPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $strategicPlanRepo->show($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function list(Request $request, StrategicPlanRepo $strategicPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $strategicPlanRepo->list($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function settingStore(Request $request, StrategicPlanRepo $strategicPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $strategicPlanRepo->settingStore($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function settingList(Request $request, StrategicPlanRepo $strategicPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $strategicPlanRepo->settingList($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
