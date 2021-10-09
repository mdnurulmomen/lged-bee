<?php

namespace App\Http\Controllers;

use App\Services\AnnualPlanMovementRevisedService;
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

    public function showAnnualPlan(Request $request, AnnualPlanRevisedService $annualPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $all_annual_plans = $annualPlanRevisedService->showAnnualPlans($request);

        if (isSuccessResponse($all_annual_plans)) {
            $response = responseFormat('success', $all_annual_plans['data']);
        } else {
            $response = responseFormat('error', $all_annual_plans['data']);
        }

        return response()->json($response);
    }

    public function showAnnualPlanEntities(Request $request, AnnualPlanRevisedService $annualPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'milestone_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $all_annual_plans = $annualPlanRevisedService->showAnnualPlanEntities($request);

        if (isSuccessResponse($all_annual_plans)) {
            $response = responseFormat('success', $all_annual_plans['data']);
        } else {
            $response = responseFormat('error', $all_annual_plans['data']);
        }

        return response()->json($response);
    }


    public function showNominatedOffices(Request $request, AnnualPlanRevisedService $annualPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'annual_plan_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $all_nominated_offices = $annualPlanRevisedService->showNominatedOffices($request);

        if (isSuccessResponse($all_nominated_offices)) {
            $response = responseFormat('success', $all_nominated_offices['data']);
        } else {
            $response = responseFormat('error', $all_nominated_offices['data']);
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

    public function exportAnnualPlan(Request $request, AnnualPlanRevisedService $annualPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'office_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $exportPlanBook = $annualPlanRevisedService->exportAnnualPlanBook($request);

        if (isSuccessResponse($exportPlanBook)) {
            $response = responseFormat('success', $exportPlanBook['data']);
        } else {
            $response = responseFormat('error', $exportPlanBook['data']);
        }

        return response()->json($response);
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


    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeApprovalAuthority(Request $request, AnnualPlanMovementRevisedService $annualPlanMovementRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'op_audit_calendar_event_id' => 'required|integer',
            'receiver_type' => 'required',
            'receiver_office_id' => 'required',
            'receiver_office_name_en' => 'required',
            'receiver_office_name_bn' => 'required',
            'receiver_unit_id' => 'required',
            'receiver_unit_name_en' => 'required',
            'receiver_unit_name_bn' => 'required',
            'receiver_officer_id' => 'required',
            'receiver_name_en' => 'required',
            'receiver_name_bn' => 'required',
            'receiver_designation_id' => 'required',
            'receiver_designation_en' => 'required',
            'receiver_designation_bn' => 'required',
            'cdesk' => 'required|json',
        ])->validate();

        $responseStore = $annualPlanMovementRevisedService->storeApprovalAuthority($request);

        if (isSuccessResponse($responseStore)) {
            $response = responseFormat('success', $responseStore['data']);
        } else {
            $response = responseFormat('error', $responseStore['data']);
        }

        return response()->json($response);
    }

    public function getMovementHistories(Request $request, AnnualPlanMovementRevisedService $annualPlanMovementRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'op_audit_calendar_event_id' => 'required|integer',
        ])->validate();

        $responseData = $annualPlanMovementRevisedService->getMovementHistories($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }
}
