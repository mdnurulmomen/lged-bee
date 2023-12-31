<?php

namespace App\Http\Controllers;

use App\Models\OpActivity;
use App\Models\OpActivityComment;
use App\Models\OpActivityMilestone;
use App\Models\OpYearlyAuditCalendarActivity;
use App\Repository\OpYearlyAuditCalendarRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OpYearlyAuditCalendarController extends Controller
{
    public function index(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendar): \Illuminate\Http\JsonResponse
    {
        try {
            $response = responseFormat('success', $opYearlyAuditCalendar->allCalendarLists($request));
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function yearsToCreateCalendar(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendar): \Illuminate\Http\JsonResponse
    {
        try {
            $response = responseFormat('success', $opYearlyAuditCalendar->yearsToCreateCalendar());
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendarRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $store_op_calendar = $opYearlyAuditCalendarRepository->storeOpYearlyAuditCalendar($request);
        if (isSuccessResponse($store_op_calendar)) {
            $response = responseFormat('success', $store_op_calendar['data']);
            return response()->json($response, 200);
        } else {
            $response = responseFormat('error', $store_op_calendar['data'], $store_op_calendar['code']);
            return response()->json($response, 500);
        }
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function showCalendarActivities(Request $request): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
        ])->validate();

        $fiscal_year = $request->fiscal_year_id;

        $milestones = OpActivity::where('fiscal_year_id', $fiscal_year)->with('milestones.milestone_calendar')->with('responsibles.office')->with('comment')->get();

        if ($milestones) {
            $response = responseFormat('success', $milestones);
        } else {
            $response = responseFormat('error', 'Activity Not Found');
        }
        return response()->json($response);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeMilestoneTargetDate(Request $request): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'milestone_id' => 'required|integer',
            'target_date' => 'required|date',
            'yearly_audit_calendar_id' => 'nullable|integer',
        ])->validate();

        $milestone =  OpActivityMilestone::find($request->milestone_id);

        try {
            $calendar = OpYearlyAuditCalendarActivity::updateOrCreate(
                ['id' => $request->yearly_audit_calendar_id],
                [
                    'duration_id' => $milestone->duration_id,
                    'outcome_id' => $milestone->outcome_id,
                    'output_id' => $milestone->output_id,
                    'activity_id' => $milestone->activity_id,
                    'fiscal_year_id' => $milestone->fiscal_year_id,
                    'milestone_id' => $request->milestone_id,
                    'op_yearly_audit_calendar_id' => 2,
                    'target_date' => $request->target_date,
                ]
            );
//            $calendar = OpYearlyAuditCalendarActivity::find($request->yearly_audit_calendar_id);
//            $calendar->target_date = $request->target_date;
//            $calendar->save();
            $response = responseFormat('success', 'Updated Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeActivityResponsible(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendar): \Illuminate\Http\JsonResponse
    {
        $data = Validator::make($request->all(), [
            'activity_id' => 'required|integer',
            'selected_office_ids' => 'required|array',
        ])->validate();

        try {
            $opYearlyAuditCalendar->storeActivityResponsible($data);
            $response = responseFormat('success', 'Successfully created');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateActivityComment(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = Validator::make($request->all(), [
            'activity_id' => 'required|integer',
            'comment_en' => 'required|string',
            'comment_bn' => 'required|string',
            'cdesk' => 'json|required',
        ])->validate();
        try {
            $opActivityComment = OpActivityComment::updateOrCreate(['activity_id' => $request->activity_id], $data);
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changeStatus(Request $request, OpYearlyAuditCalendarRepository $opYearlyCalendar): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'id' => 'required|integer',
            'status' => 'required|string',
        ])->validate();

        try {
            $opYearlyCalendar->changeStatus($request);
            return response()->json(responseFormat('success', 'Status Changed'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function pendingEventsForPublishing(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendarRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'calendar_id' => 'required|integer',
        ])->validate();

        $res = $opYearlyAuditCalendarRepository->pendingEventsForPublishing($request);

        return response()->json($res);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function yearlyAuditCalendarEventList(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendarRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'activity_type' => 'nullable',
        ])->validate();

        $res = $opYearlyAuditCalendarRepository->yearlyAuditCalendarEventList($request);

        return response()->json($res);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function publishCalendar(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendarRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'calendar_id' => 'required|integer',
            'office_ids' => 'array|required',
        ])->validate();

        $res = $opYearlyAuditCalendarRepository->publishPendingEvents($request);

        $success = $res['success'];
        $error = $res['error'];

        if (count($success) > 0) {
            $response = [
                'status' => 'success',
                'data' => ['published' => $success, 'failed' => $error],
            ];
        } else {
            $response = [
                'status' => 'error',
                'data' => $error,
            ];
        }

        return response()->json($response);
    }
}
