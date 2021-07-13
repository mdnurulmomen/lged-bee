<?php

namespace App\Http\Controllers;

use App\Models\OpActivity;
use App\Models\OpYearlyAuditCalendar;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Models\XResponsibleOffice;
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

        $milestones = OpActivity::where('fiscal_year_id', $fiscal_year)->with('milestones.milestone_calendar')->with('responsibles.office')->get();

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
    public function storeMilestoneTargetDate(Request $request)
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
            $auditCalendar = OpYearlyAuditCalendar::select('id', 'duration_id', 'fiscal_year_id', 'outcome_id', 'output_id', 'activity_id',)->where('activity_id', $data['activity_id'])->first()->toArray();
            $auditCalendar['op_yearly_audit_calendar_id'] = $auditCalendar['id'];
            unset($auditCalendar['id']);

//            $responsibles = Menu::whereIn('id', $assignedMenus)->get();

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
//    public function assignMap(Request $request): \Illuminate\Http\JsonResponse
//    {
//        $assignedMenus = $request->input('menus') ?: [];
//        $role_id = $request->input('role_id');
//        $menus = Menu::whereIn('id', $assignedMenus)->get();
//        $role = Role::find($role_id);
//
//        if ($role->menus()->sync($menus))
//            return response()->json(['msg' => 'মেনু প্রদান করা হয়েছে।', 'status' => 'success'], 200);
//        else
//            return response()->json(['msg' => 'Error', 'status' => 'error'], 500);
//
//    }
}

