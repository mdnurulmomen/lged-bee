<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApOfficeOrder;
use App\Models\ApOfficeOrderMovement;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\OpActivity;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Models\XFiscalYear;
use App\Models\XResponsibleOffice;
use App\Traits\GenericData;
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
            $auditPlanList = ApEntityIndividualAuditPlan::has('audit_teams')
                ->with(['annual_plan','audit_teams','office_order'])
                ->where('status','approved')
                ->paginate(20);

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
            $officeOrder = ApOfficeOrder::where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->first();

            $auditTeamAllMembers = AuditVisitCalenderPlanMember::distinct()
                ->select('team_member_name_bn','team_member_name_en','team_member_designation_bn',
                    'team_member_designation_en','team_member_role_bn','team_member_role_en','mobile_no')
                ->where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
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
                'memorandum_date' => date('Y-m-d',strtotime($request->memorandum_date)),
                'heading_details' => $request->heading_details,
                'advices' => $request->advices,
                'approved_status' => $request->approved_status,
                'order_cc_list' => $request->order_cc_list,
                'draft_officer_id' => $cdesk->officer_id,
                'draft_officer_name_en' => $cdesk->officer_en,
                'draft_officer_name_bn' => $cdesk->officer_bn,
                'draft_designation_id' => $cdesk->designation_id,
                'draft_designation_name_en' => $cdesk->designation_en,
                'draft_designation_name_bn' => $cdesk->designation_bn,
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
        //return ['status' => 'error', 'data' => $request->all()];

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
}
