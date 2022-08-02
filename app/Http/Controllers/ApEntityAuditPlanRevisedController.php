<?php

namespace App\Http\Controllers;

use App\Models\ApEntityAuditPlan;
use App\Services\ApEntityAuditPlanRevisedService;
use App\Services\ApEntityTeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApEntityAuditPlanRevisedController extends Controller
{
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

    public function previouslyAssignedDesignations(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'activity_id' => 'required|integer',
            'fiscal_year_id' => 'required|integer',
            'office_id' => 'required|integer',
        ])->validate();
        $team_list = $apEntityAuditPlanRevisedService->getPreviouslyAssignedDesignations($request);

        if (isSuccessResponse($team_list)) {
            $response = responseFormat('success', $team_list['data']);
        } else {
            $response = responseFormat('error', $team_list['data']);
        }
        return response()->json($response);
    }

    public function getAuditPlanWiseTeam(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'activity_id' => 'required|integer',
            'annual_plan_id' => 'required|integer',
            'fiscal_year_id' => 'required|integer',
            'audit_plan_id' => 'required|integer',
        ])->validate();
        $team_list = $apEntityAuditPlanRevisedService->getAuditPlanWiseTeam($request);

        if (isSuccessResponse($team_list)) {
            $response = responseFormat('success', $team_list['data']);
        } else {
            $response = responseFormat('error', $team_list['data']);
        }
        return response()->json($response);
    }

    public function getTeamInfo(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'team_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();
        $team_info = $apEntityAuditPlanRevisedService->getTeamInfo($request);
        if (isSuccessResponse($team_info)) {
            $response = responseFormat('success', $team_info['data']);
        } else {
            $response = responseFormat('error', $team_info['data']);
        }
        return response()->json($response);
    }


    public function getPlanWiseTeamMembers(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'audit_plan_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();
        $team_info = $apEntityAuditPlanRevisedService->getPlanWiseTeamMembers($request);
        if (isSuccessResponse($team_info)) {
            $response = responseFormat('success', $team_info['data']);
        } else {
            $response = responseFormat('error', $team_info['data']);
        }
        return response()->json($response);
    }

    public function getPlanWiseTeamSchedules(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'audit_plan_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();
        $team_info = $apEntityAuditPlanRevisedService->getPlanWiseTeamSchedules($request);
        if (isSuccessResponse($team_info)) {
            $response = responseFormat('success', $team_info['data']);
        } else {
            $response = responseFormat('error', $team_info['data']);
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

    public function storeAuditTeam(Request $request, ApEntityTeamService $apEntityTeamService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'annual_plan_id' => 'required|integer',
            'audit_plan_id' => 'integer',
            'teams' => 'required',
        ])->validate();

        $add_audit_team = $apEntityTeamService->storeAuditTeam($request);

        if (isSuccessResponse($add_audit_team)) {
            $response = responseFormat('success', 'Successfully Saved Team');
        } else {
            $response = responseFormat('error', $add_audit_team['data']);
        }
        return response()->json($response);
    }

    public function updateAuditTeam(Request $request, ApEntityTeamService $apEntityTeamService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'activity_id' => 'required|integer',
            'annual_plan_id' => 'required|integer',
            'audit_plan_id' => 'required|integer',
            'teams' => 'required',
        ])->validate();

        $add_audit_team = $apEntityTeamService->updateAuditTeam($request);

        if (isSuccessResponse($add_audit_team)) {
            $response = responseFormat('success', $add_audit_team['data']);
        } else {
            $response = responseFormat('error', $add_audit_team['data']);
        }
        return response()->json($response);
    }

    public function storeTeamSchedule(Request $request, ApEntityTeamService $apEntityTeamService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'audit_plan_id' => 'integer',
            'annual_plan_id' => 'integer',
            'team_schedules' => 'required|json',
        ])->validate();

        $add_audit_team = $apEntityTeamService->storeTeamSchedule($request);

        if (isSuccessResponse($add_audit_team)) {
            $response = responseFormat('success', 'Successfully Saved Schedule');
        } else {
            $response = responseFormat('error', $add_audit_team['data']);
        }
        return response()->json($response);
    }

    public function updateTeamSchedule(Request $request, ApEntityTeamService $apEntityTeamService): \Illuminate\Http\JsonResponse
    {

        Validator::make($request->all(), [
            'annual_plan_id' => 'required|integer',
            'audit_plan_id' => 'required|integer',
            'team_schedules' => 'required|json',
        ])->validate();

        $add_audit_team = $apEntityTeamService->updateTeamSchedule($request);

        if (isSuccessResponse($add_audit_team)) {
            $response = responseFormat('success', 'Successfully Saved Schedule');
        } else {
            $response = responseFormat('error', $add_audit_team['data']);
        }
        return response()->json($response);
    }

    public function teamLogDiscard(Request $request, ApEntityTeamService $apEntityTeamService)
    {
        Validator::make($request->all(), [
            'audit_plan_id' => 'required|integer',
        ])->validate();

        $teamLogDiscard = $apEntityTeamService->teamLogDiscard($request);

        if (isSuccessResponse($teamLogDiscard)) {
            $response = responseFormat('success', 'Schedule Discard Successfully');
        } else {
            $response = responseFormat('error', $teamLogDiscard['data']);
        }
        return response()->json($response);
    }

    public function auditPlanAuditEditLock(Request $request, ApEntityAuditPlanRevisedService $apEntityAuditPlanRevisedService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'audit_plan_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $team_info = $apEntityAuditPlanRevisedService->auditPlanAuditEditLock($request);

        if (isSuccessResponse($team_info)) {
            $response = responseFormat('success', $team_info['data']);
        } else {
            $response = responseFormat('error', $team_info['data']);
        }
        return response()->json($response);
    }
}
