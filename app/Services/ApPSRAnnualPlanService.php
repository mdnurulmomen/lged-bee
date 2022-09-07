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
        $plan_no = 1;
        try {
            $annualPlanPSR = new AnnualPlanPSR();
            // $annualPlanPSR->psr_plan_id = $request->psr_plan_id;
            $annualPlanPSR->plan_no = $request->plan_no + 1;
            $annualPlanPSR->annual_plan_id = $request->annual_plan_id;
            $annualPlanPSR->plan_description = $request->plan_description;
            $annualPlanPSR->save();
            return ['status' => 'success', 'data' => 'Save successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function view(Request $request): array
    {
        // return ['status' => 'error', 'data' => $request->psr_plan_id];

        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ?: $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $psr = AnnualPlanPSR::where('id',$request->psr_plan_id)
            ->first()->toArray();
            return ['status' => 'success', 'data' => $psr];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

}
