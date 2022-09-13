<?php

namespace App\Http\Controllers;
use App\Services\ApPSRAnnualPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApAnnualPlanPSRController extends Controller
{
    public function store(Request $request, ApPSRAnnualPlanService $apEntityTeamService): \Illuminate\Http\JsonResponse
    {
        $psrResponse = $apEntityTeamService->store($request);

        if (isSuccessResponse($psrResponse)) {
            $response = responseFormat('success', $psrResponse['data']);
        } else {
            $response = responseFormat('error', $psrResponse['data']);
        }
        return response()->json($response);
    }
    public function view(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        $edit_plan = $ApPSRAnnualPlanService->view($request);

        if (isSuccessResponse($edit_plan)) {
            $response = responseFormat('success', $edit_plan['data']);
        } else {
            $response = responseFormat('error', $edit_plan['data']);
        }
        return response()->json($response);
    }

    public function edit(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        $psr_plan = $ApPSRAnnualPlanService->editpsrplan($request);

        if (isSuccessResponse($psr_plan)) {
            $response = responseFormat('success', $psr_plan['data']);
        } else {
            $response = responseFormat('error', $psr_plan['data']);
        }
        return response()->json($response);
    }
}
