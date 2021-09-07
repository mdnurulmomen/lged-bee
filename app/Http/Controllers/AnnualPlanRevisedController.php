<?php

namespace App\Http\Controllers;

use App\Services\AnnualPlanRevisedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnualPlanRevisedController extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function allAnnualPlan(Request $request, AnnualPlanRevisedService $annualPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $all_annual_plans = $annualPlanRevisedService->allAnnualPlans($request);

        if (isSuccessResponse($all_annual_plans)) {
            $response = responseFormat('success', $all_annual_plans['data']);
        } else {
            $response = responseFormat('error', $all_annual_plans['data']);
        }

        return response()->json($response);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeAnnualPlan(Request $request, AnnualPlanRevisedService $annualPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $storeAnnualPlan = $annualPlanRevisedService->storeAnnualPlan($request);

        if (isSuccessResponse($storeAnnualPlan)) {
            $response = responseFormat('success', $storeAnnualPlan['data']);
        } else {
            $response = responseFormat('error', $storeAnnualPlan['data']);
        }

        return response()->json($response);
    }

    public function exportAnnualPlan(Request $request)
    {

    }

    public function submitToOCAG(Request $request, AnnualPlanRevisedService $annualPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $submit_plans = $annualPlanRevisedService->submitPlanToOCAG($request);
        if (isSuccessResponse($submit_plans)) {
            $response = responseFormat('success', $submit_plans['data']);
        } else {
            $response = responseFormat('error', $submit_plans['data']);
        }

        return response()->json($response);
    }
}
