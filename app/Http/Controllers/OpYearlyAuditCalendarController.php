<?php

namespace App\Http\Controllers;

use App\Models\OpActivity;
use App\Models\OpActivityComment;
use App\Models\OpYearlyAuditCalendarActivity;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Models\XResponsibleOffice;
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
            'yearly_audit_calendar_id' => 'required|integer',
        ])->validate();

        try {
            $calendar = OpYearlyAuditCalendarActivity::find($request->yearly_audit_calendar_id);
            $calendar->target_date = $request->target_date;
            $calendar->save();
            $response = responseFormat('success', 'Updated Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeActivityResponsible(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = Validator::make($request->all(), [
            'activity_id' => 'required|integer',
            'selected_office_ids' => 'required|array',
        ])->validate();

        try {
            $auditCalendar = OpYearlyAuditCalendarActivity::select('id', 'duration_id', 'fiscal_year_id', 'outcome_id', 'output_id', 'activity_id', 'op_yearly_audit_calendar_id')->where('activity_id', $data['activity_id'])->first()->toArray();
            $auditCalendar['op_yearly_audit_calendar_activity_id'] = $auditCalendar['id'];
            unset($auditCalendar['id']);

            foreach ($data['selected_office_ids'] as $responsible_id) {
                if ($responsible_id) {
                    $office = XResponsibleOffice::select("office_id", "office_layer", "office_name_en", "office_name_bn", "short_name_en", "short_name_bn")->where('id', $responsible_id)->first()->toArray();
                    $creatingData = array_merge($office, $auditCalendar);
                    $createAuditResponsible = OpYearlyAuditCalendarResponsible::create($creatingData);
                    $response = responseFormat('success', 'Successfully created');
                }
            }
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
            'created_by' => 'nullable|integer',
            'modified_by' => 'nullable|integer',
        ])->validate();
        try {
            $activityComment = OpActivityComment::where('activity_id', $request->activity_id)->first();
            if ($activityComment) {
                $activityComment->update($data);
            } else {
                OpActivityComment::create($data);
            }
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

    public function saveEventsBeforePublishing(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendarRepository): \Illuminate\Http\JsonResponse
    {
        $res = $opYearlyAuditCalendarRepository->saveEventsBeforePublishing($request);

        return response()->json(responseFormat('success', $res));
    }
}
