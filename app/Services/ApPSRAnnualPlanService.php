<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AnnualPlanPSR;
use Illuminate\Http\Request;
use App\Traits\ApiHeart;
use App\Traits\GenericData;

class ApPSRAnnualPlanService
{
    use GenericData, ApiHeart;

    public function store(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $annualPlanPSR = empty($request->psr_plan_id) ? new AnnualPlanPSR() : AnnualPlanPSR::find($request->psr_plan_id);
            $annualPlanPSR->annual_plan_id = $request->annual_plan_id;
            $annualPlanPSR->activity_id = $request->activity_id;
            $annualPlanPSR->fiscal_year_id = $request->fiscal_year_id;
            $annualPlanPSR->status = $request->status;
            $annualPlanPSR->plan_description = $request->plan_description;
            $annualPlanPSR->created_by = $cdesk->officer_id;
            $annualPlanPSR->save();
            return ['status' => 'success', 'data' => $annualPlanPSR->id];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function view(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $psr = AnnualPlanPSR::where('id',$request->psr_plan_id)->first()->toArray();
            return ['status' => 'success', 'data' => $psr];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function update(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $annualPlanPSR = AnnualPlanPSR::find($request->psr_id);
            $annualPlanPSR->annual_plan_id = $request->annual_plan_id ? $request->annual_plan_id : $annualPlanPSR->annual_plan_id;
            $annualPlanPSR->activity_id = $request->activity_id ? $request->activity_id : $annualPlanPSR->activity_id;
            $annualPlanPSR->fiscal_year_id = $request->fiscal_year_id ? $request->fiscal_year_id : $annualPlanPSR->fiscal_year_id;
            $annualPlanPSR->status = $request->status ? $request->status : $annualPlanPSR->status;
            $annualPlanPSR->is_sent_cag = $request->is_sent_cag ? $request->is_sent_cag : $annualPlanPSR->is_sent_cag;
            $annualPlanPSR->plan_description = $request->plan_description ? $request->plan_description : $annualPlanPSR->plan_description;
            $annualPlanPSR->created_by = $cdesk->officer_id;
            $annualPlanPSR->modified_by = $cdesk->officer_id;
            $annualPlanPSR->save();

            return ['status' => 'success', 'data' => 'Send Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function sendToOcag(Request $request): array
    {
        try {

            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            AnnualPlan::whereIn('id',$request->annual_plan_ids)
                ->update(['is_sent_cag' => 1, 'status' => 'pending']);

            return ['status' => 'success', 'data' => 'Send Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPsrApprovalList(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($request->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $psr_approval_plan_list =  AnnualPlan::select('id','subject_matter','status')->where('fiscal_year_id',$request->fiscal_year_id)
                ->where('activity_type',$request->activity_type)
                ->where('is_sent_cag',1)
                ->get();

            return ['status' => 'success', 'data' => $psr_approval_plan_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function approvePsrTopic(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($request->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $psr_approval_plan_list =  AnnualPlan::where('id',$request->annual_plan_id)
                ->update(['status' => 'approved']);

            return ['status' => 'success', 'data' => $psr_approval_plan_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPsrReportApprovalList(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($request->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $psr_approval_plan_list =  AnnualPlanPSR::select('id','annual_plan_id','status')
                ->with('annual_plan:id,subject_matter')
                ->where('fiscal_year_id',$request->fiscal_year_id)
                ->where('is_sent_cag',1)
                ->get();

            return ['status' => 'success', 'data' => $psr_approval_plan_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
