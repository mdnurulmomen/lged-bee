<?php

namespace App\Http\Controllers;

use App\Services\AuditVisitCalendarPlanService;
use Illuminate\Http\Request;

class AuditVisitCalenderPlanController extends Controller
{
    public function getVisitPlanCalendar(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {

        $calendar_data = $auditVisitCalendarPlanService->getVisitPlanCalendar($request);

        if (isSuccessResponse($calendar_data)) {
            $response = responseFormat('success', $calendar_data['data']);
        } else {
            $response = responseFormat('error', $calendar_data['data']);
        }

        return response()->json($response);
    }

    public function updateVisitCalenderStatus(Request $request, AuditVisitCalendarPlanService $auditVisitCalendarPlanService): \Illuminate\Http\JsonResponse
    {
        $updateStatus= $auditVisitCalendarPlanService->updateVisitCalenderStatus($request);
        if (isSuccessResponse($updateStatus)) {
            $response = responseFormat('success', $updateStatus['data']);
        } else {
            $response = responseFormat('error', $updateStatus['data']);
        }

        return response()->json($response);
    }
}
