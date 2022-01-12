<?php

namespace App\Services;

use App\Models\ApEntityIndividualAuditPlan;
use App\Models\Apotti;
use App\Models\ApottiRAirMap;
use App\Models\AuditTemplate;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\RAir;
use App\Models\RAirMovement;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditAIRReportService
{
    use GenericData, ApiHeart;

    public function loadApprovePlanList(Request $request): array
    {
        $air_type = $request->air_type;
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
                ->with('air_reports', function ($query) use($air_type) {
                    return $query->select('id','audit_plan_id','status')->where('type',$air_type);
                })
                ->select('id','annual_plan_id','schedule_id','activity_id','fiscal_year_id','created_at')
                ->whereHas('office_order', function($query){
                    $query->where('approved_status', 'approved');
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

    public function editAirReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $airReport = RAir::find($request->air_report_id)->toArray();
            return ['status' => 'success', 'data' => $airReport];
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
                'type' => $request->type,
                'status' => $request->status,
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

            //for apotti
            if (!empty($request->apottis)){
                Apotti::whereIn('id',$request->all_apottis)->update(['air_generate_type'=> null]);
                Apotti::whereIn('id',$request->apottis)->update(['air_generate_type'=>'preliminary']);

                $mappingData = [];
                foreach ($request->apottis as $apotti){
                    array_push($mappingData,[
                        'apotti_id' => $apotti,
                        'rairs_id' => $airId
                    ]);
                }

                if (!empty($mappingData)){
                    ApottiRAirMap::where('rairs_id',$airId)->delete();
                    ApottiRAirMap::insert($mappingData);
                }
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


    public function getAuditApottiList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $auditApottis = Apotti::select('id','audit_plan_id','apotti_title','apotti_description','apotti_type','onucched_no','total_jorito_ortho_poriman','total_onishponno_jorito_ortho_poriman','response_of_rpu','irregularity_cause','audit_conclusion','audit_recommendation','apotti_sequence','air_generate_type')
                ->where('fiscal_year_id',$request->fiscal_year_id)
                ->where('audit_plan_id',$request->audit_plan_id);

            if ($request->air_type == 'preliminary'){
                $auditApottis = $auditApottis->whereNull('air_generate_type');
            }
            $responseData['auditApottis'] = $auditApottis->get()->toArray();


            $responseData['auditMapApottis'] = ApottiRAirMap::with('apotti_map_list')
                ->where('rairs_id',$request->air_id)
                ->get()->toArray();
            return ['status' => 'success', 'data' => $responseData];

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

            if (empty($request->apottis)){
                return ['status' => 'error', 'data' => []];
            }

            $auditApottis = Apotti::select('id','audit_plan_id','apotti_title','apotti_description','apotti_type','onucched_no','total_jorito_ortho_poriman','total_onishponno_jorito_ortho_poriman','response_of_rpu','irregularity_cause','audit_conclusion','audit_recommendation','apotti_sequence')
                ->whereIn('id',$request->apottis)
                ->get()
                ->toArray();

            return ['status' => 'success', 'data' => $auditApottis];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    //movement
    public function storeAirMovement(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            RAir::where('id',$request->r_air_id)->update(['status'=> $request->status]);

            //air movement data
            $airMovementData = [
                'r_air_id'  => $request->r_air_id,
                'receiver_officer_id'  => $request->receiver_officer_id,
                'receiver_office_id'  => $request->receiver_office_id,
                'receiver_unit_id'  => $request->receiver_unit_id,
                'receiver_unit_name_en'  => $request->receiver_unit_name_en,
                'receiver_unit_name_bn'  => $request->receiver_unit_name_bn,
                'receiver_employee_id'  => $request->receiver_employee_id,
                'receiver_employee_name_en'  => $request->receiver_employee_name_en,
                'receiver_employee_name_bn'  => $request->receiver_employee_name_bn,
                'receiver_employee_designation_id'  => $request->receiver_employee_designation_id,
                'receiver_employee_designation_en'  => $request->receiver_employee_designation_en,
                'receiver_employee_designation_bn'  => $request->receiver_employee_designation_bn,
                'receiver_officer_phone'  => $request->receiver_officer_phone,
                'receiver_officer_email'  => $request->receiver_officer_email,
                'sender_officer_id'  => $cdesk->officer_id,
                'sender_office_id'  => $cdesk->office_id,
                'sender_unit_id'  => $cdesk->office_unit_id,
                'sender_unit_name_en'  => $cdesk->office_unit_en,
                'sender_unit_name_bn'  => $cdesk->office_unit_bn,
                'sender_employee_id'  => $cdesk->officer_id,
                'sender_employee_name_en'  => $cdesk->officer_en,
                'sender_employee_name_bn'  => $cdesk->officer_bn,
                'sender_employee_designation_id'  => $cdesk->designation_id,
                'sender_employee_designation_en'  => $cdesk->designation_en,
                'sender_employee_designation_bn'  => $cdesk->designation_bn,
                'sender_officer_phone'  => $cdesk->phone,
                'sender_officer_email'  => $cdesk->email,
                'comments'  => $request->comments
            ];

            RAirMovement::create($airMovementData);

            return ['status' => 'success', 'data' => ['apottis' => $request->apottis]];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function getAirLastMovement(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $lastAirMovementInfo = RAirMovement::select('id','r_air_id','receiver_officer_id','receiver_unit_name_en','receiver_unit_name_bn','receiver_employee_name_en','receiver_employee_name_bn','receiver_employee_designation_id','receiver_employee_designation_en','receiver_employee_designation_bn')
                ->where('r_air_id',$request->r_air_id)
                ->latest()
                ->first()
                ->toArray();

            return ['status' => 'success', 'data' => $lastAirMovementInfo];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}