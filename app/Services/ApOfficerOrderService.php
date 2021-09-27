<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApOfficeOrder;
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
            $auditPlanList = ApEntityIndividualAuditPlan::with(['annual_plan'])->get();
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
            $officeOrder = ApOfficeOrder::where('audit_plan_id',$request->audit_plan_id)->first();
            $responseData = ['status' => 'success', 'data' => $officeOrder];
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
            $auditPlan = ApEntityIndividualAuditPlan::find($request->audit_plan_id);

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
                'approved_status' => '',
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

            ApOfficeOrder::updateOrcreate($data);
            $responseData = ['status' => 'success', 'data' => 'Successfully Office Order Generated!'];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }
}
