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
}
