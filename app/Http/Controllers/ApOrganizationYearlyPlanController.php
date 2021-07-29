<?php

namespace App\Http\Controllers;

use App\Repository\ApOrganizationYearlyPlanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApOrganizationYearlyPlanController extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function allAnnualPlan(Request $request, ApOrganizationYearlyPlanRepository $apOrganizationYearlyPlanRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $all_annual_plans = $apOrganizationYearlyPlanRepository->allAnnualPlans($request);

        if (isSuccessResponse($all_annual_plans)) {
            $response = responseFormat('success', $all_annual_plans['data']);
        } else {
            $response = responseFormat('error', $all_annual_plans['data']);
        }

        return response()->json($response);
    }
}
