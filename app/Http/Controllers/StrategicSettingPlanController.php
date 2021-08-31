<?php

namespace App\Http\Controllers;

use App\Repository\StrategicSettingPlanRepo;
use Illuminate\Http\Request;

class StrategicSettingPlanController extends Controller
{

    public function store(Request $request, StrategicSettingPlanRepo $strategicPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $strategicPlanRepo->store($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function list(Request $request, StrategicSettingPlanRepo $strategicPlanRepo): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $strategicPlanRepo->list($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

}
