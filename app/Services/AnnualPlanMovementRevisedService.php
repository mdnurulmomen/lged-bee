<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AnnualPlanApproval;
use App\Models\AnnualPlanMain;
use App\Models\AnnualPlanMovement;
use App\Models\OpOrganizationYearlyAuditCalendarEvent;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Models\XFiscalYear;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use DB;
class AnnualPlanMovementRevisedService
{
    use GenericData;

    public function sendAnnualPlanSenderToReceiver(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        \DB::beginTransaction();
        try {
            $xFiscalYear = XFiscalYear::where('id',$request->fiscal_year_id)->first();
            $isExistApprovalAuthority = AnnualPlanMovement::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('op_audit_calendar_event_id', $request->op_audit_calendar_event_id)
                ->where('annual_plan_main_id', $request->annual_plan_main_id)
                ->where('activity_type', $request->activity_type)
                ->where('sender_office_id', $cdesk->office_id)
                ->exists();

            if ($isExistApprovalAuthority){
                AnnualPlanApproval::where('op_audit_calendar_event_id', $request->op_audit_calendar_event_id)
                    ->where('annual_plan_main_id', $request->annual_plan_main_id)
                    ->where('office_id', $cdesk->office_id)
                    ->update(["approval_status" => 'pending']);
            }
            else{
                $annual_plan_approval = New AnnualPlanApproval();
                $annual_plan_approval->fiscal_year_id = $request->fiscal_year_id;
                $annual_plan_approval->office_id = $cdesk->office_id;
                $annual_plan_approval->office_en = $cdesk->office_name_en;
                $annual_plan_approval->office_bn = $cdesk->office_name_bn;
                $annual_plan_approval->op_audit_calendar_event_id = $request->op_audit_calendar_event_id;
                $annual_plan_approval->annual_plan_main_id = $request->annual_plan_main_id;
                $annual_plan_approval->activity_type = $request->activity_type;
                $annual_plan_approval->approval_status = 'pending';
                $annual_plan_approval->save();
            }

            AnnualPlanMain::where('id',$request->annual_plan_main_id)->update(['approval_status' => 'pending']);

            $data = [
                'fiscal_year_id' => $request->fiscal_year_id,
                'op_audit_calendar_event_id' => $request->op_audit_calendar_event_id,
                'annual_plan_main_id' => $request->annual_plan_main_id,
                'activity_type' => $request->activity_type,
                'duration_id' => $xFiscalYear->duration_id,

                'sender_office_id' => $cdesk->office_id,
                'sender_office_name_en' => $cdesk->office_name_en,
                'sender_office_name_bn' => $cdesk->office_name_bn,
                'sender_unit_id' => $cdesk->office_unit_id,
                'sender_unit_name_en' => $cdesk->office_unit_en,
                'sender_unit_name_bn' => $cdesk->office_unit_bn,
                'sender_officer_id' => $cdesk->officer_id,
                'sender_name_en' => $cdesk->officer_en,
                'sender_name_bn' => $cdesk->officer_bn,
                'sender_designation_id' => $cdesk->designation_id,
                'sender_designation_en' => $cdesk->designation_en,
                'sender_designation_bn' => $cdesk->designation_bn,

                'receiver_type' => $request->receiver_type,
                'receiver_office_id' => $request->receiver_office_id,
                'receiver_office_name_en' => $request->receiver_office_name_en,
                'receiver_office_name_bn' => $request->receiver_office_name_bn,
                'receiver_unit_id' => $request->receiver_unit_id,
                'receiver_unit_name_en' => $request->receiver_unit_name_en,
                'receiver_unit_name_bn' => $request->receiver_unit_name_bn,
                'receiver_officer_id' => $request->receiver_officer_id,
                'receiver_name_en' => $request->receiver_name_en,
                'receiver_name_bn' => $request->receiver_name_bn,
                'receiver_designation_id' => $request->receiver_designation_id,
                'receiver_designation_en' => $request->receiver_designation_en,
                'receiver_designation_bn' => $request->receiver_designation_bn,

                'status' => $request->status,
                'comments' => $request->comments
            ];
            AnnualPlanMovement::create($data);
            \DB::commit();
            $responseData = ['status' => 'success', 'data' => 'Successfully Sent'];
        } catch (\Exception $exception) {
            \DB::rollback();
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function sendAnnualPlanReceiverToSender(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $xFiscalYear = XFiscalYear::where('id',$request->fiscal_year_id)->first();
            $annualPlanMovement = AnnualPlanMovement::where('fiscal_year_id',$request->fiscal_year_id)
                ->where('op_audit_calendar_event_id',$request->op_audit_calendar_event_id)
                ->where('duration_id',$xFiscalYear->duration_id)
                ->where('sender_office_id',$request->office_id)
                ->where('receiver_office_id',$cdesk->office_id)
                ->where('status','pending')
                ->latest()
                ->first();

            if ($annualPlanMovement){
                $data = [
                    'fiscal_year_id' => $request->fiscal_year_id,
                    'op_audit_calendar_event_id' => $request->op_audit_calendar_event_id,
                    'duration_id' => $xFiscalYear->duration_id,
                    'annual_plan_main_id' => $request->annual_plan_main_id,
                    'activity_type' => $request->activity_type,
                    'sender_office_id' => $cdesk->office_id,
                    'sender_office_name_en' => $cdesk->office_name_en,
                    'sender_office_name_bn' => $cdesk->office_name_bn,
                    'sender_unit_id' => $cdesk->office_unit_id,
                    'sender_unit_name_en' => $cdesk->office_unit_en,
                    'sender_unit_name_bn' => $cdesk->office_unit_bn,
                    'sender_officer_id' => $cdesk->officer_id,
                    'sender_name_en' => $cdesk->officer_en,
                    'sender_name_bn' => $cdesk->officer_bn,
                    'sender_designation_id' => $cdesk->designation_id,
                    'sender_designation_en' => $cdesk->designation_en,
                    'sender_designation_bn' => $cdesk->designation_bn,

                    'receiver_type' => $request->receiver_type,
                    'receiver_office_id' => $annualPlanMovement->sender_office_id,
                    'receiver_office_name_en' => $annualPlanMovement->sender_office_name_en,
                    'receiver_office_name_bn' => $annualPlanMovement->sender_office_name_bn,
                    'receiver_unit_id' => $annualPlanMovement->sender_unit_id,
                    'receiver_unit_name_en' => $annualPlanMovement->sender_unit_name_en,
                    'receiver_unit_name_bn' => $annualPlanMovement->sender_unit_name_bn,
                    'receiver_officer_id' => $annualPlanMovement->sender_officer_id,
                    'receiver_name_en' => $annualPlanMovement->sender_name_en,
                    'receiver_name_bn' => $annualPlanMovement->sender_name_bn,
                    'receiver_designation_id' => $annualPlanMovement->sender_designation_id,
                    'receiver_designation_en' => $annualPlanMovement->sender_designation_en,
                    'receiver_designation_bn' => $annualPlanMovement->sender_designation_bn,

                    'status' => $request->status,
                    'comments' => $request->comments
                ];

                AnnualPlanMovement::create($data);

                //update op organization yearly audit calendar event
//                OpOrganizationYearlyAuditCalendarEvent::where("id", $request->op_audit_calendar_event_id)
//                    ->update(["approval_status" => $request->status]);

                AnnualPlanApproval::where("annual_plan_main_id", $request->annual_plan_main_id)
                    ->where('office_id',$request->office_id)
                    ->update(["approval_status" => $request->status]);

                AnnualPlanMain::where("id", $request->annual_plan_main_id)
                    ->update(["approval_status" => $request->status]);

                if($request->status == 'approved'){
                    $activity_list = AnnualPlan::Where('fiscal_year_id',$request->fiscal_year_id)
                        ->select(['activity_id',DB::raw("COUNT(id) as total_plan"),DB::raw("SUM(nominated_man_power_counts) as staff_assign"), DB::raw("SUM(budget) as budget")])
                        ->groupBy('activity_id')
                        ->get();
                    foreach ($activity_list as $activity){
                        OpYearlyAuditCalendarResponsible::where('office_id',$request->office_id)->where('activity_id',$activity['activity_id'])->update(['assigned_staffs' => $activity['staff_assign'] ? $activity['staff_assign'] : 0,'assigned_budget' => $activity['budget'] ? $activity['budget'] : 0,'total_plan' => $activity['total_plan'] ? $activity['total_plan'] : 0]);
                    }
                }

                $responseData = ['status' => 'success', 'data' => 'Successfully Saved!'];
            }
            else{
                $responseData = ['status' => 'error', 'data' => 'Invalid receiver'];
            }
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function getMovementHistories(Request $request): array
    {
        try{
            $annualPlanMovementList = AnnualPlanMovement::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('op_audit_calendar_event_id', $request->op_audit_calendar_event_id)
                ->get();
            $responseData = ['status' => 'success', 'data' => $annualPlanMovementList];
        }catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $responseData;
    }
    public function getScheduleInfo(Request $request): array
    {
//        return ['status' => 'success', 'data' => $request->cdesk];

        $cdesk = json_decode($request->cdesk, false);
        try{

            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $ScheduleInfo = OpOrganizationYearlyAuditCalendarEventSchedule::where('id',$request->schedule_id)->first();
            $responseData = ['status' => 'success', 'data' => $ScheduleInfo];
        }catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $responseData;
    }


    public function submitMilestoneValue(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try{
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            OpOrganizationYearlyAuditCalendarEventSchedule::where("id", $request->schedule_id)->where('fiscal_year_id',$request->fiscal_year_id)
            ->update(["no_of_items" => $request->no_of_items, 'staff_assigne' => $request->staff_assigne]);

            $schedule_data = OpOrganizationYearlyAuditCalendarEventSchedule::where('fiscal_year_id',$request->fiscal_year_id)
                ->where('activity_id',$request->activity_id)
                ->select(DB::raw("SUM(staff_assigne) as staff_assigne"),DB::raw("SUM(no_of_items) as no_of_items"))
                ->first();

            OpYearlyAuditCalendarResponsible::where('fiscal_year_id',$request->fiscal_year_id)
                ->where('fiscal_year_id',$request->fiscal_year_id)
                ->where('activity_id',$request->activity_id)
                ->where('office_id',$cdesk->office_id)
                ->update(['assigned_staffs' => $schedule_data->staff_assigne,'total_plan' => $schedule_data->no_of_items]);

            $responseData = ['status' => 'success', 'data' => 'Calender Update Successfully'];

        }catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $responseData;
    }

    public function getCurrentDeskApprovalAuthority(Request $request): array
    {
        try{
            $currentDeskApprovalAuthority = AnnualPlanMovement::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('op_audit_calendar_event_id', $request->op_audit_calendar_event_id)
                ->where('receiver_type','approver')
                ->latest()
                ->first();
            $responseData = ['status' => 'success', 'data' => $currentDeskApprovalAuthority];
        }catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $responseData;
    }
}
