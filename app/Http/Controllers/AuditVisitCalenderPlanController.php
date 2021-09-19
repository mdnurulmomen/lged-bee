<?php

namespace App\Http\Controllers;

use App\Services\AuditVisitCalendarPlanService;
use Illuminate\Http\Request;

class AuditVisitCalenderPlanController extends Controller
{
    public function getIndividualPlanCalendar(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {
        $calendar_data = $auditVisitCalendarPlanService->getIndividualPlanCalendar($request);

        if (isSuccessResponse($calendar_data)) {
            $response = responseFormat('success', $calendar_data['data']);
        } else {
            $response = responseFormat('error', $calendar_data['data']);
        }

        return response()->json($response);
    }

    public function storeIndividualPlanCalendar(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {
        $storeIndividualCalendar = $auditVisitCalendarPlanService->storeAuditTeamCalendar($request);
        if (isSuccessResponse($storeIndividualCalendar)) {
            $response = responseFormat('success', $storeIndividualCalendar['data']);
        } else {
            $response = responseFormat('error', $storeIndividualCalendar['data']);
        }

        return response()->json($response);
    }
}
