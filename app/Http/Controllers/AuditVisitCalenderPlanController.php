<?php

namespace App\Http\Controllers;

use App\Services\AuditVisitCalendarPlanService;
use Illuminate\Http\Request;

class AuditVisitCalenderPlanController extends Controller
{
    public function getTeamVisitPlanCalendar(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {
        $individual_plan = $auditVisitCalendarPlanService->getTeamVisitPlanCalendar($request);
        if (isSuccessResponse($individual_plan)) {
            $response = responseFormat('success', $individual_plan['data']);
        } else {
            $response = responseFormat('error', $individual_plan['data']);
        }

        return response()->json($response);
    }

    public function teamCalenderFilter(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {
        $individual_plan = $auditVisitCalendarPlanService->teamCalenderFilter($request);
        if (isSuccessResponse($individual_plan)) {
            $response = responseFormat('success', $individual_plan['data']);
        } else {
            $response = responseFormat('error', $individual_plan['data']);
        }

        return response()->json($response);
    }

    public function updateVisitCalenderStatus(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {
        $updateStatus = $auditVisitCalendarPlanService->updateVisitCalenderStatus($request);
        if (isSuccessResponse($updateStatus)) {
            $response = responseFormat('success', $updateStatus['data']);
        } else {
            $response = responseFormat('error', $updateStatus['data']);
        }

        return response()->json($response);
    }

    public function fiscalYearWiseTeams(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService)
    {

        \Validator::make($request->all(), ['fiscal_year_id' => 'integer|required', 'office_id' => 'integer|required'])->validate();

        $all_teams = $auditVisitCalendarPlanService->fiscalYearWiseTeams($request);

        if (isSuccessResponse($all_teams)) {
            $response = responseFormat('success', $all_teams['data']);
        } else {
            $response = responseFormat('error', $all_teams['data']);
        }
        return response()->json($response);
    }

    public function fiscalYearCostCenterWiseTeams(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService)
    {

        \Validator::make($request->all(), ['fiscal_year_id' => 'integer|required', 'office_id' => 'integer|required','cost_center_id' => 'integer|nullable'])->validate();

        $all_teams = $auditVisitCalendarPlanService->fiscalYearCostCenterWiseTeams($request);

        if (isSuccessResponse($all_teams)) {
            $response = responseFormat('success', $all_teams['data']);
        } else {
            $response = responseFormat('error', $all_teams['data']);
        }
        return response()->json($response);
    }


    public function scheduleEntityFiscalYearWise(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService)
    {

        \Validator::make($request->all(), ['fiscal_year_id' => 'integer|required', 'office_id' => 'integer|required'])->validate();

        $all_teams = $auditVisitCalendarPlanService->scheduleEntityFiscalYearWise($request);

        if (isSuccessResponse($all_teams)) {
            $response = responseFormat('success', $all_teams['data']);
        } else {
            $response = responseFormat('error', $all_teams['data']);
        }
        return response()->json($response);
    }

    public function costCenterAndFiscalYearWiseTeams(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService)
    {

        \Validator::make($request->all(), ['fiscal_year_id' => 'integer|required', 'office_id' => 'integer|required', 'cost_center_id' => 'integer|required'])->validate();

        $all_teams = $auditVisitCalendarPlanService->costCenterAndFiscalYearWiseTeams($request);

        if (isSuccessResponse($all_teams)) {
            $response = responseFormat('success', $all_teams['data']);
        } else {
            $response = responseFormat('error', $all_teams['data']);
        }
        return response()->json($response);
    }

    public function getSubTeam(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {
        \Validator::make($request->all(), [
            'team_id' => 'required|integer',
            'office_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();
//        dd($request->all());
        $sub_team_list = $auditVisitCalendarPlanService->getSubTeam($request);

        if (isSuccessResponse($sub_team_list)) {
            $response = responseFormat('success', $sub_team_list['data']);
        } else {
            $response = responseFormat('error', $sub_team_list['data']);
        }
        return response()->json($response);
    }

    public function getCostCenterDirectorateFiscalYearWise(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService)
    {
        \Validator::make($request->all(), ['fiscal_year_id' => 'integer|required', 'office_id' => 'integer|required'])->validate();

        $all_cost_center = $auditVisitCalendarPlanService->getCostCenterDirectorateFiscalYearWise($request);

        if (isSuccessResponse($all_cost_center)) {
            $response = responseFormat('success', $all_cost_center['data']);
        } else {
            $response = responseFormat('error', $all_cost_center['data']);
        }
        return response()->json($response);
    }

    public function teamCalenderScheduleList(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {
        $individual_plan = $auditVisitCalendarPlanService->teamCalenderScheduleList($request);
        if (isSuccessResponse($individual_plan)) {
            $response = responseFormat('success', $individual_plan['data']);
        } else {
            $response = responseFormat('error', $individual_plan['data']);
        }

        return response()->json($response);
    }
}
