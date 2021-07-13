<?php

namespace App\Http\Controllers;

use App\Http\Requests\OperationalPlan\Operational;
use App\Repository\OperationalPlan;

class OperationalPlanController extends Controller
{
    public function operationalPlan(
        Operational $request,
        OperationalPlan $op
    ): \Illuminate\Http\JsonResponse {
        try {
            $response = responseFormat('success', $op->OperationalPlan($request));
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
