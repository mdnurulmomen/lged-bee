<?php

namespace App\Http\Controllers;

use App\Models\ApEntityAuditPlan;
use App\Services\ApEntityAuditPlanRevisedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApEntityAuditPlanRevisedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $all_plans = $apEntityAuditPlanRevisedService->allEntityAuditPlanLists($request);

        if (isSuccessResponse($all_plans)) {
            $response = responseFormat('success', $all_plans['data']);
        } else {
            $response = responseFormat('error', $all_plans['data']);
        }
        return response()->json($response);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNewAuditPlan(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'activity_id' => 'required|integer',
            'annual_plan_id' => 'required|integer',
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $add_plan = $apEntityAuditPlanRevisedService->createNewAuditPlan($request);

        if (isSuccessResponse($add_plan)) {
            $response = responseFormat('success', $add_plan['data']);
        } else {
            $response = responseFormat('error', $add_plan['data']);
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService
     * @return \Illuminate\Http\JsonResponse
     */
    public function editAuditPlan(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'audit_plan_id' => 'required|integer',
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $edit_plan = $apEntityAuditPlanRevisedService->editAuditPlan($request);

        if (isSuccessResponse($edit_plan)) {
            $response = responseFormat('success', $edit_plan['data']);
        } else {
            $response = responseFormat('error', $edit_plan['data']);
        }
        return response()->json($response);
    }

    public function store(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        return response()->json([]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function show(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'party_id' => 'required|integer',
            'yearly_plan_rp_id' => 'required|integer',
            'cdesk' => 'required|json',
            'lang' => 'string',
        ])->validate();

        $show_plan = $apEntityAuditPlanRevisedService->showEntityAuditPlan($request);

        if (isSuccessResponse($show_plan)) {
            $response = responseFormat('success', $show_plan['data']);
        } else {
            $response = responseFormat('error', $show_plan['data']);
        }
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'activity_id' => 'required|integer',
            'annual_plan_id' => 'required|integer',
            'audit_plan_id' => 'sometimes|integer',
            'plan_description' => 'required',
            'cdesk' => 'required|json',
        ])->validate();

        $add_plan = $apEntityAuditPlanRevisedService->update($request);

        if (isSuccessResponse($add_plan)) {
            $add_plan = $add_plan['data']['id'];
            $response = responseFormat('success', $add_plan);
        } else {
            $response = responseFormat('error', $add_plan['data']);
        }
        return response()->json($response);
    }

    public function getSubTeam(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'team_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();
//        dd($request->all());
        $sub_team_list = $apEntityAuditPlanRevisedService->getSubTeam($request);

        if (isSuccessResponse($sub_team_list)) {
            $response = responseFormat('success', 'Successfully Saved Plan');
        } else {
            $response = responseFormat('error', $sub_team_list['data']);
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ApEntityAuditPlan $apEntityAuditPlan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ApEntityAuditPlan $apEntityAuditPlan)
    {
        return response()->json('');
    }

    public function storeAuditTeam(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'annual_plan_id' => 'required|integer',
            'audit_plan_id' => 'required|integer',
            'entity_id' => 'required|integer',
            'entity_name_en' => 'required|string',
            'entity_name_bn' => 'required|string',
            'team_start_date' => 'required',
            'team_end_date' => 'required',
            'team_members' => 'required|string',
            'leader_name_en' => 'required|string',
            'leader_name_bn' => 'required|string',
            'leader_designation_id' => 'required|integer',
            'leader_designation_name_en' => 'required|string',
            'leader_designation_name_bn' => 'required|string',
            'audit_year_start' => 'required',
            'audit_year_end' => 'required|integer',
            'approve_status' => 'required|string',
        ])->validate();

        $add_audit_team = $apEntityAuditPlanRevisedService->storeAuditTeam($request);

        if (isSuccessResponse($add_audit_team)) {
            $response = responseFormat('success', 'Successfully Saved Team');
        } else {
            $response = responseFormat('error', $add_audit_team['data']);
        }
        return response()->json($response);
    }
}
