<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApOfficeOrder;
use App\Models\ApOfficeOrderMovement;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\GenericData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApOfficerOrderService
{
    use GenericData;

    public function auditPlanList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);

        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $fiscal_year_id = $request->fiscal_year_id;
            $activity_id = $request->activity_id;

            $query = ApEntityIndividualAuditPlan::query();

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($activity_id, function ($q, $activity_id) {
                $q->whereHas('office_order', function ($q) use ($activity_id) {
                    return $q->where('activity_id', $activity_id);
                });
            });

            $auditPlanList =  $query->has('audit_teams')
                ->with(['annual_plan.ap_entities','audit_teams','office_order.office_order_movement'])
                ->where('status','approved')
                ->paginate($request->per_page ?: config('bee_config.per_page_pagination'));

//            if ($request->per_page && $request->page && !$request->all) {
//                $auditPlanList = ApEntityIndividualAuditPlan::has('audit_teams')
//                    ->with(['annual_plan.ap_entities','audit_teams','office_order.office_order_movement'])
//                    ->where('fiscal_year_id', $request->fiscal_year_id)
//                    ->where('status','approved')
//                    ->paginate($request->per_page);
//            }
//            else{
//                $auditPlanList = ApEntityIndividualAuditPlan::has('audit_teams')
//                    ->with(['annual_plan.ap_entities','audit_teams','office_order.office_order_movement'])
//                    ->where('fiscal_year_id', $request->fiscal_year_id)
//                    ->where('status','approved')
//                    ->get();
//            }

            $responseData = ['status' => 'success', 'data' => $auditPlanList];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }


    public function showOfficeOrder(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $officeOrder = ApOfficeOrder::with(['office_order_movement'])->where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->first();

            $auditTeamAllMembers = AuditVisitCalenderPlanMember::distinct()
                ->select('team_member_name_bn','team_member_name_en','team_member_designation_bn',
                    'team_member_designation_en','team_member_role_bn','team_member_role_en','mobile_no')
                ->where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->orderBy('team_member_role_en','DESC')
                ->get()
                ->toArray();

            $auditTeamWiseSchedule = AuditVisitCalendarPlanTeam::where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->get();

            $officeOrderInfo = [
                'office_order' => $officeOrder,
                'audit_team_members' => $auditTeamAllMembers,
                'audit_team_schedules' => $auditTeamWiseSchedule,
            ];

            $responseData = ['status' => 'success', 'data' => $officeOrderInfo];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }


    public function generateOfficeOrder(Request $request): array
    {

       //return ['status' => 'error', 'data' =>date('Y/m/d',strtotime($request->memorandum_date))];

        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $annualPlan = AnnualPlan::find($request->annual_plan_id);

            //audit plan
            $auditPlan = ApEntityIndividualAuditPlan::find($request->audit_plan_id);
            $auditPlan->has_office_order = 1;
            $auditPlan->save();

            $data = [
                'annual_plan_id' => $request->annual_plan_id,
                'schedule_id' => $auditPlan->schedule_id,
                'activity_id' => $auditPlan->activity_id,
                'milestone_id' => $auditPlan->milestone_id,
                'fiscal_year_id' => $auditPlan->fiscal_year_id,
                'audit_plan_id' => $request->audit_plan_id,
                'duration_id' => $annualPlan->activity->duration_id,
                'outcome_id' => $annualPlan->activity->outcome_id,
                'output_id' => $annualPlan->activity->output_id,
                'memorandum_no' => $request->memorandum_no,
                'memorandum_date' => $request->memorandum_date,
                'heading_details' => $request->heading_details,
                'advices' => $request->advices,
                'approved_status' => $request->approved_status,
                'order_cc_list' => $request->order_cc_list,
                'cc_sender_details' => $request->cc_sender_details,
                'draft_officer_id' => $cdesk->officer_id,
                'draft_officer_name_en' => $cdesk->officer_en,
                'draft_officer_name_bn' => $cdesk->officer_bn,
                'draft_designation_id' => $cdesk->designation_id,
                'draft_designation_name_en' => $cdesk->designation_en,
                'draft_designation_name_bn' => $cdesk->designation_bn,
                'draft_office_unit_id' => $cdesk->office_unit_id,
                'draft_office_unit_en' => $cdesk->office_unit_en,
                'draft_office_unit_bn' => $cdesk->office_unit_bn,
                'draft_officer_phone' => $cdesk->phone,
                'draft_officer_email' => $cdesk->email,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];

            ApOfficeOrder::updateOrcreate(['annual_plan_id' => $request->annual_plan_id,
                'audit_plan_id' => $request->audit_plan_id],$data);
            $responseData = ['status' => 'success', 'data' => 'Successfully Office Order Generated!'];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function storeOfficeOrderApprovalAuthority(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $data = [
                'ap_office_order_id' => $request->ap_office_order_id,
                'annual_plan_id' => $request->annual_plan_id,
                'audit_plan_id' => $request->audit_plan_id,
                'office_id' => $request->office_id,
                'unit_id' => $request->unit_id,
                'unit_name_en' => $request->unit_name_en,
                'unit_name_bn' => $request->unit_name_bn,
                'officer_type' => $request->officer_type,
                'employee_id' => $request->employee_id,
                'employee_name_en' => $request->employee_name_en,
                'employee_name_bn' => $request->employee_name_bn,
                'employee_designation_id' => $request->employee_designation_id,
                'employee_designation_en' => $request->employee_designation_en,
                'employee_designation_bn' => $request->employee_designation_bn,
                'officer_phone' => $request->officer_phone,
                'officer_email' => $request->officer_email,
                'received_by' => $request->received_by,
                'sent_by' => $cdesk->officer_id,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];

            ApOfficeOrderMovement::updateOrcreate(['ap_office_order_id' => $request->ap_office_order_id,
                'annual_plan_id' => $request->annual_plan_id,
                'audit_plan_id' => $request->audit_plan_id,
                'officer_type' => $request->officer_type
            ],$data);
            $responseData = ['status' => 'success', 'data' => 'Successfully Saved!'];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function approveOfficeOrder(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $apOfficeOrder = ApOfficeOrder::find($request->ap_office_order_id);
            $apOfficeOrder->approved_status = $request->approved_status;
            $apOfficeOrder->save();
            $responseData = ['status' => 'success', 'data' => 'Successfully Saved!'];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }
}
