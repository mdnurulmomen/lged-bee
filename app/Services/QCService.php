<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\Apotti;
use App\Models\AuditTemplate;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\RAir;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
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
                ->with('air_reports:id,audit_plan_id')
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
            $auditTemplate= AuditTemplate::where('template_type', $request->template_type)
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
            $airData = [
                'fiscal_year_id' => $request->fiscal_year_id,
                'annual_plan_id' => $request->annual_plan_id,
                'audit_plan_id' => $request->audit_plan_id,
                'activity_id' => $request->activity_id,
                'air_description' => $request->air_description,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];
            if($request->air_id){
                RAir::where('id',$request->air_id)->update($airData);
                $airId =$request->air_id;
            }else{
                $storeAirData = RAir::create($airData);
                $airId = $storeAirData->id;
            }
            return ['status' => 'success', 'data' => ['air_id' => $airId]];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function getAuditTeam(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $auditTeamMembers = AuditVisitCalenderPlanMember::distinct()
                ->select('team_member_name_bn','team_member_name_en','team_member_designation_bn',
                    'team_member_designation_en','team_member_role_bn','team_member_role_en','mobile_no')
                ->where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->orderBy('team_member_role_en','DESC')
                ->get()
                ->toArray();
            return ['status' => 'success', 'data' => $auditTeamMembers];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAuditTeamSchedule(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $auditTeamSchedule = AuditVisitCalendarPlanTeam::where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->get()
                ->toArray();
            return ['status' => 'success', 'data' => $auditTeamSchedule];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function getAuditApotti(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $auditApottis= Apotti::where('fiscal_year_id',$request->fiscal_year_id)
                ->where('audit_plan_id',$request->audit_plan_id)
                ->get()
                ->toArray();
            return ['status' => 'success', 'data' => $auditApottis];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
