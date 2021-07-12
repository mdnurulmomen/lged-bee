<?php

namespace App\Http\Controllers;

use App\Models\OpActivity;
use App\Models\OpYearlyAuditCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OpAuditCalendarController extends Controller
{
    public function index(Request $request)
    {
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function activityMilestones(Request $request): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
        ])->validate();

        $fiscal_year = $request->fiscal_year_id;

        $milestones = OpActivity::where('fiscal_year_id', $fiscal_year)->with('milestones.milestone_calendar')->get();

        if ($milestones) {
            $response = responseFormat('success', $milestones);
        } else {
            $response = responseFormat('error', 'Activity Milestone Not Found');
        }
        return response()->json($response);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeTargetDate(Request $request)
    {
        Validator::make($request->all(), [
            'milestone_id' => 'required|integer',
            'target_date' => 'required|date',
            'yearly_audit_calendar_id' => 'required|integer',
        ])->validate();

        try {
            $calendar = OpYearlyAuditCalendar::find($request->yearly_audit_calendar_id);
            $calendar->target_date = $request->target_date;
            $calendar->save();
            $response = responseFormat('success', 'Updated Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return $response;
    }
}
