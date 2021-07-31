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

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storePlanAssignedDetails(Request $request, ApOrganizationYearlyPlanRepository $apOrganizationYearlyPlanRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'schedule_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'milestone_id' => 'required|integer',
            'budget' => 'required|integer',
            'designations' => 'required|json',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ])->validate();

        $ap_plan_submit = $apOrganizationYearlyPlanRepository->storeAnnualPlanDetails($request);

        if (isSuccessResponse($ap_plan_submit)) {
            $response = responseFormat('success', $ap_plan_submit['data']);
        } else {
            $response = responseFormat('error', $ap_plan_submit['data']);
        }

        return response()->json($response);

    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeSelectedRPEntities(Request $request, ApOrganizationYearlyPlanRepository $apOrganizationYearlyPlanRepository)
    {
        Validator::make($request->all(), [
            'schedule_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'milestone_id' => 'required|integer',
            'cdesk' => 'required|json',
            'selected_entities' => 'required|json',
        ])->validate();

        $add_rp_entites = $apOrganizationYearlyPlanRepository->storeSelectedRPEntities($request);

        if (isSuccessResponse($add_rp_entites)) {
            $response = responseFormat('success', $add_rp_entites['data']);
        } else {
            $response = responseFormat('error', $add_rp_entites['data']);
        }

        return response()->json($response);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function allSelectedRPEntities(Request $request, ApOrganizationYearlyPlanRepository $apOrganizationYearlyPlanRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'schedule_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'milestone_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $selected_rp_entities = $apOrganizationYearlyPlanRepository->allSelectedRPEntities($request);

        if (isSuccessResponse($selected_rp_entities)) {
            $response = responseFormat('success', $selected_rp_entities['data']);
        } else {
            $response = responseFormat('error', $selected_rp_entities['data']);
        }

        return response()->json($response);
    }
}
