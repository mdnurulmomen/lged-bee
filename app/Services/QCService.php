<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\AuditTemplate;
use App\Models\RAir;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QCService
{
    use GenericData, ApiHeart;

    public function loadApprovePlanList(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;
        $cdesk = json_decode($request->cdesk, false);

        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $annualPlanQuery = ApEntityIndividualAuditPlan::with('annual_plan:id,office_type,total_unit_no,subject_matter')
                ->with('annual_plan.ap_entities:id,annual_plan_id,ministry_name_bn,ministry_name_en,entity_name_bn,entity_name_en')
                ->with('office_order:id,audit_plan_id,memorandum_no,memorandum_date,approved_status')
                ->select('id','annual_plan_id','schedule_id','activity_id','fiscal_year_id','created_at')
                ->whereHas('office_order', function($q){
                    $q->where('approved_status', 'approved');
                })
                ->where('fiscal_year_id', $fiscal_year_id);

            if ($request->per_page && $request->page && !$request->all) {
                $annualPlanQuery = $annualPlanQuery->paginate($request->per_page);
            } else {
                $annualPlanQuery = $annualPlanQuery->get();
            }
            return ['status' => 'success', 'data' => $annualPlanQuery];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function createNewAIRReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $auditTemplate = AuditTemplate::where('template_type', $request->template_type)
                ->where('lang', 'bn')->first()->toArray();

            return ['status' => 'success', 'data' => $auditTemplate];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeAirReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $rAir = new RAir();
            $rAir->fiscal_year_id = $request->fiscal_year_id;
            $rAir->annual_plan_id = $request->annual_plan_id;
            $rAir->audit_plan_id = $request->audit_plan_id;
            $rAir->activity_id = $request->activity_id;
            $rAir->air_description = $request->air_description;
            $rAir->created_by = $request->created_by;
            $rAir->modified_by = $request->modified_by;
            $rAir->save();
            return ['status' => 'success', 'data' => 'Air Report Successfully Saved'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
