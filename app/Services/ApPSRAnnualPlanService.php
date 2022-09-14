<?php

namespace App\Services;

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
        $office_id = $request->office_id ?: $cdesk->office_id;
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
    public function editpsrplan(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            // $user_details = $cdesk->officer_bn.','.$cdesk->designation_bn;

//            return ['status' => 'error', 'data' => $cdesk->officer_id];

            $edit_psr = AnnualPlanPSR::find($request->annual_plan_id);
// dd($edit_psr);
            if($edit_psr){
            $annualPlanPSR->psr_plan_id = $request->psr_plan_id;
            $annualPlanPSR->activity_id = $request->activity_id;
            $annualPlanPSR->fiscal_year_id = $request->fiscal_year_id;
            $annualPlanPSR->status = $request->status;
            $annualPlanPSR->plan_description = $request->plan_description;
            $annualPlanPSR->created_by = $cdesk->officer_id;
            $annualPlanPSR->save();
            return ['status' => 'success', 'data' => $annualPlanPSR->annual_plan_id];
            }

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

}
