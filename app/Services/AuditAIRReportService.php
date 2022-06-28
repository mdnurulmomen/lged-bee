<?php

namespace App\Services;

use App\Models\AcMemoPorisishto;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\ApottiPorisishto;
use App\Models\ApottiRAirMap;
use App\Models\ApottiStatus;
use App\Models\AuditTemplate;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\RAir;
use App\Models\RAirMovement;
use App\Models\RTemplateContent;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Carbon\Carbon;
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
                ->with('fiscal_year:id,duration_id,start,end,description')
                ->with('annual_plan.ap_entities:id,annual_plan_id,ministry_id,ministry_name_bn,ministry_name_en,entity_id,entity_name_bn,entity_name_en')
                ->with('office_order:id,audit_plan_id,memorandum_no,memorandum_date,approved_status')
                ->with('air_reports', function ($query) use ($air_type) {
                    return $query->with(['latest_r_air_movement'])->select('id', 'audit_plan_id', 'status')->where('type', $air_type);
                })
                ->select('id', 'annual_plan_id', 'schedule_id', 'activity_id', 'fiscal_year_id', 'created_at')
                ->whereHas('office_order', function ($query) {
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
            $auditTemplate = AuditTemplate::where('template_type', $request->template_type)
                ->where('lang', 'bn')->first()->toArray();
            return ['status' => 'success', 'data' => $auditTemplate];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function editAirReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $airReport = RAir::with('latest_r_air_movement')
                ->with('fiscal_year:id,duration_id,start,end,description')
                ->with('annual_plan:id,office_type,total_unit_no,subject_matter')
                ->with('annual_plan.ap_entities:id,annual_plan_id,ministry_id,ministry_name_bn,ministry_name_en,entity_id,entity_name_bn,entity_name_en')
                ->where('id', $request->air_report_id)
                ->first()
                ->toArray();
            return ['status' => 'success', 'data' => $airReport];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeAirReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        \DB::beginTransaction();
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            //return ['status' => 'success', 'data' => ['data' => $contents]];

            $airData = [
                'report_type' => 'generated',
                'report_name' => $request->entity_name_bn,
                'fiscal_year_id' => $request->fiscal_year_id,
                'activity_id' => $request->activity_id,
                'annual_plan_id' => $request->annual_plan_id,
                'audit_plan_id' => $request->audit_plan_id,
                'ministry_id' => $request->ministry_id,
                'ministry_name_en' => $request->ministry_name_en,
                'ministry_name_bn' => $request->ministry_name_bn,
                'entity_id' => $request->entity_id,
                'entity_name_en' => $request->entity_name_en,
                'entity_name_bn' => $request->entity_name_bn,
                'air_description' => $request->air_description,
                'type' => $request->type,
                'status' => $request->status,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];
            if ($request->air_id) {
                RAir::where('id', $request->air_id)->update($airData);
                $airId = $request->air_id;
            } else {
                $storeAirData = RAir::create($airData);
                $airId = $storeAirData->id;
            }

            //delete template content
            $air_type = $request->status . '_air';
            RTemplateContent::where('relational_id', $airId)->where('template_type', $air_type)->delete();

            //template content
            $contents = [];
            $content_list = gzuncompress(getDecryptedData(($request->air_description)));
            foreach (json_decode($content_list, true) as $content) {
                if ($content['content_key'] != 'audit_porisisto_details') {
                    $contents[] = [
                        'relational_id' => $airId,
                        'template_type' => $air_type,
                        'content_key' => $content['content_key'],
                        'content_value' => base64_encode($content['content']),
                    ];
                }
            }
            RTemplateContent::insert($contents);


            //for apotti
            if (!empty($request->apottis)) {
                Apotti::whereIn('id', $request->all_apottis)->update(['air_generate_type' => null]);
                Apotti::whereIn('id', $request->apottis)->update(['air_generate_type' => 'preliminary']);

                $mappingData = [];
                foreach ($request->apottis as $apotti) {
                    $mappingData[] = [
                        'apotti_id' => $apotti,
                        'rairs_id' => $airId
                    ];
                }

                if (!empty($mappingData)) {
                    ApottiRAirMap::where('rairs_id', $airId)->delete();
                    ApottiRAirMap::insert($mappingData);
                }
            }
            \DB::commit();
            return ['status' => 'success', 'data' => ['air_id' => $airId]];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function updateQACAirReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $airData = [
                'report_type' => 'generated',
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];

            if ($request->air_description) {
                $airData['air_description'] = $request->air_description;
            }

            if ($request->status) {
                $airData['status'] = $request->status;
            }

            if ($request->approved_date) {
                $airData['approved_date'] = date('Y-m-d', strtotime($request->approved_date));
            }

            if ($request->is_bg_press) {
                $airData['is_bg_press'] = $request->is_bg_press;
            }

            if ($request->is_printing_done) {
                $airData['is_printing_done'] = $request->is_printing_done;
                $map_apottis = ApottiRAirMap::where('rairs_id', $request->air_id)->pluck('apotti_id');

                $approved_apottis =  ApottiStatus::whereIn('apotti_id', $map_apottis)
                    ->where('apotti_type', 'approved')
                    ->where('qac_type', 'cqat')
                    ->pluck('apotti_id');

                $rpu_data['directorate_id'] = $office_id;
                $rpu_data['approved_apottis'] = $approved_apottis;

                $send_status_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.apotti_final_status_update_to_rpu'), $rpu_data)->json();
            }

            if ($request->comment) {
                $airData['comment'] = $request->comment;
            }

            if ($request->approval_status) {
                $airData['approval_status'] = $request->approval_status;
            }

            if ($request->final_approval_status) {
                $airData['final_approval_status'] = $request->final_approval_status;
            }

            if ($request->qac_report_date) {
                $airData['qac_report_date'] = $request->qac_report_date;
            }

            RAir::where('id', $request->air_id)->update($airData);

            //template content
            if ($request->air_type){
                $contents = [];
                $content_list = gzuncompress(getDecryptedData(($request->air_description)));
                foreach (json_decode($content_list, true) as $content) {
                    if ($content['content_key'] != 'audit_porisisto_details') {
                        $contents[] = [
                            'relational_id' => $request->air_id,
                            'template_type' => $request->air_type,
                            'content_key' => $content['content_key'],
                            'content_value' => base64_encode($content['content']),
                        ];
                    }
                }
                RTemplateContent::insert($contents);
            }


            //$response_data = $this->sendApottiStatusToRpu($request->air_id,$cdesk);

            return ['status' => 'success', 'data' => ['air_id' => $request->all()]];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    //    public function sendApottiStatusToRpu($air_id,$cdesk_info){
    //        try {
    //            $office_db_con_response = $this->switchOffice($cdesk_info->office_id);
    //            if (!isSuccessResponse($office_db_con_response)) {
    //                return ['status' => 'error', 'data' => $office_db_con_response];
    //            }
    //
    //            $auditTeamMembers = ApottiRAirMap::where('rairs_id',$air_id)->get()->toArray();
    //
    //            return $auditTeamMembers;
    //
    //        } catch (\Exception $exception) {
    //            return ['status' => 'error', 'data' => $exception->getMessage()];
    //        }
    //    }

    public function getAuditTeam(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $auditTeamMembers = AuditVisitCalenderPlanMember::distinct()
                ->select(
                    'team_member_name_bn',
                    'team_member_name_en',
                    'team_member_designation_bn',
                    'team_member_designation_en',
                    'team_member_role_bn',
                    'team_member_role_en',
                    'mobile_no',
                    'employee_grade'
                )
                ->where('audit_plan_id', $request->audit_plan_id)
                ->where('annual_plan_id', $request->annual_plan_id)
                ->orderBy('employee_grade', 'ASC')
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
            $auditTeamSchedule = AuditVisitCalendarPlanTeam::where('audit_plan_id', $request->audit_plan_id)
                ->where('annual_plan_id', $request->annual_plan_id)
                ->get()
                ->toArray();
            return ['status' => 'success', 'data' => $auditTeamSchedule];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAirWiseContentKey(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $contents = RTemplateContent::where('relational_id', $request->relational_id)
                ->where('template_type', $request->template_type)
                ->get();

            $result = [];
            foreach ($contents as $content){
                $result[$content['content_key']] = base64_decode($content['content_value']);
            }

            return ['status' => 'success', 'data' => $result];
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

            $auditApottis = Apotti::select('id', 'audit_plan_id', 'apotti_title', 'apotti_description', 'apotti_type', 'onucched_no', 'total_jorito_ortho_poriman', 'total_onishponno_jorito_ortho_poriman', 'response_of_rpu', 'irregularity_cause', 'audit_conclusion', 'audit_recommendation', 'apotti_sequence', 'air_generate_type')
                ->where('fiscal_year_id', $request->fiscal_year_id)
                ->where('audit_plan_id', $request->audit_plan_id)
                ->where('parent_office_id', $request->entity_id);

            if ($request->air_type == 'preliminary') {
                $auditApottis = $auditApottis->whereNull('air_generate_type');
            }

            $responseData['auditApottis'] = $auditApottis->orderBy('onucched_no')->get()->toArray();

            $responseData['auditMapApottis'] = ApottiRAirMap::with('apotti_map_data')
                ->where('rairs_id', $request->air_id)
                ->get()->toArray();

            return ['status' => 'success', 'data' => $responseData];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAirWiseAuditApottiList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $preliminaryAir = RAir::with(['r_air_child', 'r_air_child.latest_r_air_movement', 'ap_entities', 'qac_committee.committee.qac_committee_members', 'fiscal_year'])->where('id', $request->air_id)->first()->toArray();
            $responseData['rAirInfo'] = $preliminaryAir;

            if ($request->qac_type == 'qac-1') {
                $responseData['apottiList'] = ApottiRAirMap::with(['apotti_map_data', 'apotti_map_data.apotti_items', 'apotti_map_data.apotti_status'])
                    ->where('rairs_id', $preliminaryAir['r_air_child']['id'])
                    ->get()->sortBy('apotti_map_data.onucched_no')
                    ->toArray();
            } elseif ($request->qac_type == 'qac-2') {
                $responseData['apottiList'] = ApottiRAirMap::with(['apotti_map_data', 'apotti_map_data.apotti_items', 'apotti_map_data.apotti_status'])
                    ->whereHas('apotti_map_data.apotti_status', function ($q) {
                        $q->where('apotti_type', 'sfi');
                    })
                    ->where('rairs_id', $preliminaryAir['r_air_child']['id'])
                    ->get()
                    ->toArray();
            } elseif ($request->qac_type == 'cqat') {
                $responseData['apottiList'] = ApottiRAirMap::with(['apotti_map_data', 'apotti_map_data.apotti_items', 'apotti_map_data.apotti_status'])
                    ->whereHas('apotti_map_data', function ($q) {
                        $q->where('apotti_type', 'draft')->orWhere('apotti_type', 'approved');
                        //                            ->where(function($query){
                        //                                $query->where('final_status','draft')
                        //                                    ->orWhere('final_status','approved');
                        //                            });
                    })
                    ->where('rairs_id', $preliminaryAir['r_air_child']['id'])
                    ->get()
                    ->toArray();
            } else {
                $responseData['apottiList'] = ApottiRAirMap::with(['apotti_map_data', 'apotti_map_data.apotti_items', 'apotti_map_data.apotti_status'])->where('rairs_id', $preliminaryAir['r_air_child']['id'])->get()->toArray();
            }
            //$qac01Apottis = ApottiRAirMap::where('rairs_id',$preliminaryAir['r_air_child']['id'])->pluck('apotti_id');
            //$responseData['apottiList'] = Apotti::with(['apotti_items','apotti_status'])->whereIn('id',$qac01Apottis)->get()->toArray();
            return ['status' => 'success', 'data' => $responseData];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    //for set qac apotti
    public function getAirWiseQACApotti(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;

        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $qacApottis = ApottiRAirMap::where('rairs_id', $request->air_id)
                ->where('is_delete', 0)->pluck('apotti_id');

            $apottiList = ApottiStatus::with('apotti')->whereIn('apotti_id', $qacApottis);

            if ($request->qac_type == 'qac-2') {
                $apottiList->where('qac_type', $request->qac_type)
                    ->where('apotti_type', 'draft');
            }
            if ($request->qac_type == 'cqat') {
                $apottiList->where('qac_type', $request->qac_type)
                    ->where('apotti_type', 'approved');
            }
            $apottiList = $apottiList->get()->toArray();

            return ['status' => 'success', 'data' => $apottiList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    //for set qac apotti
    public function getAirAndApottiTypeWiseQACApotti(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $qacApottis = ApottiRAirMap::where('rairs_id', $request->air_id)
                ->where('is_delete', 0)
                ->pluck('apotti_id');

            $apottiList = ApottiStatus::with('apotti')->whereIn('apotti_id', $qacApottis)
                ->where('apotti_type', $request->apotti_type)
                ->where('qac_type', $request->qac_type)
                ->get()
                ->sortBy('apotti.onucched_no')
                ->toArray();

            return ['status' => 'success', 'data' => $apottiList];
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

            $auditApottis = Apotti::select('id', 'audit_plan_id', 'apotti_title', 'apotti_description', 'apotti_type', 'onucched_no', 'total_jorito_ortho_poriman', 'total_onishponno_jorito_ortho_poriman', 'response_of_rpu', 'irregularity_cause', 'audit_conclusion', 'audit_recommendation', 'apotti_sequence')
                ->whereIn('id', $request->apottis)
                ->orderBy('onucched_no', 'ASC')
                ->get()
                ->toArray();

            return ['status' => 'success', 'data' => $auditApottis];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAuditApottiWisePrisistos(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $apottis = json_decode($request->apottis);
            $apotti_items = ApottiItem::with(['porisishtos'])->whereIn('apotti_id', $apottis)->paginate(5);
            return ['status' => 'success', 'data' => $apotti_items];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function getAirWisePorisistos(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $qacApottis = ApottiRAirMap::where('rairs_id', $request->air_id)
                ->where('is_delete', 0)
                ->pluck('apotti_id');

            /*$apotti_items = ApottiItem::with(['apotti:id,onucched_no','porisishtos'])
                ->whereIn('apotti_id', $qacApottis)
                ->orderBy(Apotti::select('onucched_no')
                    ->whereColumn('apottis.id', 'apotti_items.apotti_id')
                );*/

            $apottis = Apotti::with(['apotti_porisishtos'])
                ->whereIn('id', $qacApottis)
                ->orderBy('onucched_no','ASC');

            if ($request->all && $request->all == 1) {
                $apottis = $apottis->get()->toArray();
            } else {
                $apottis = $apottis->paginate($request->per_page ?: 5);
            }
            return ['status' => 'success', 'data' => $apottis];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    //movement
    public function storeAirMovement(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ?: $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            RAir::where('id', $request->r_air_id)->update(['status' => $request->status]);

            //air movement data
            $airMovementData = [
                'r_air_id' => $request->r_air_id,
                'receiver_officer_id' => $request->receiver_officer_id,
                'receiver_office_id' => $request->receiver_office_id,
                'receiver_unit_id' => $request->receiver_unit_id,
                'receiver_unit_name_en' => $request->receiver_unit_name_en,
                'receiver_unit_name_bn' => $request->receiver_unit_name_bn,
                'receiver_employee_id' => $request->receiver_employee_id,
                'receiver_employee_name_en' => $request->receiver_employee_name_en,
                'receiver_employee_name_bn' => $request->receiver_employee_name_bn,
                'receiver_employee_designation_id' => $request->receiver_employee_designation_id,
                'receiver_employee_designation_en' => $request->receiver_employee_designation_en,
                'receiver_employee_designation_bn' => $request->receiver_employee_designation_bn,
                'receiver_officer_phone' => $request->receiver_officer_phone,
                'receiver_officer_email' => $request->receiver_officer_email,
                'sender_officer_id' => $cdesk->officer_id,
                'sender_office_id' => $cdesk->office_id,
                'sender_unit_id' => $cdesk->office_unit_id,
                'sender_unit_name_en' => $cdesk->office_unit_en,
                'sender_unit_name_bn' => $cdesk->office_unit_bn,
                'sender_employee_id' => $cdesk->officer_id,
                'sender_employee_name_en' => $cdesk->officer_en,
                'sender_employee_name_bn' => $cdesk->officer_bn,
                'sender_employee_designation_id' => $cdesk->designation_id,
                'sender_employee_designation_en' => $cdesk->designation_en,
                'sender_employee_designation_bn' => $cdesk->designation_bn,
                'sender_officer_phone' => $cdesk->phone,
                'sender_officer_email' => $cdesk->email,
                'comments' => $request->comments
            ];

            RAirMovement::create($airMovementData);
            if (!empty($request->receiver_officer_id)) {
                $rAirData = RAir::where('id', $request->r_air_id)->first()->toArray();
                //Create Task for Approval
                $task_data = [
                    'task_assignee' => [
                        'user_email' => $request->receiver_officer_email,
                        'user_phone' => $request->receiver_officer_phone,
                        'user_name_en' => $request->receiver_officer_en,
                        'user_name_bn' => $request->receiver_officer_bn,
                        'user_officer_id' => $request->receiver_officer_id,
                        'username' => $request->receiver_user_id,
                        'user_office_id' => $request->receiver_office_id,
                        'user_office_name_en' => $cdesk->office_name_en,
                        'user_office_name_bn' => $cdesk->office_name_bn,
                        'user_unit_id' => $request->receiver_unit_id,
                        'user_office_unit_name_en' => $request->receiver_unit_name_en,
                        'user_office_unit_name_bn' => $request->receiver_unit_name_bn,
                        'user_designation_id' => $request->receiver_employee_designation_id,
                        'user_designation_name_en' => $request->receiver_employee_designation_en,
                        'user_designation_name_bn' => $request->receiver_employee_designation_bn,
                        'user_type' => 'assigned',
                    ],
                    'task_title_en' => $rAirData['report_name'],
                    'task_title_bn' => $rAirData['report_name'],
                    'description' => $request->comments,
                    'meta_data' => base64_encode(json_encode(['r_air_id' => $request->r_air_id, 'return_url' => ''])),
                    'task_start_end_date_time' => Carbon::now()->format('d/m/Y H:i A') . ' - ' . Carbon::now()->addDay()->format('d/m/Y H:i A'),
                    'notifications' => json_encode([[
                        "medium" => "email",
                        "interval" => "30",
                        "unit" => "minutes",
                    ]]),
                ];

                //(new AmmsPonjikaServices())->createTask($task_data, $cdesk);
                //end task creation for approval

            }
            //for qac 01 insert
            if ($request->status == 'approved' && $request->air_type != 'cqat') {

                $newAirType = 'draft';
                if ($request->air_type == 'preliminary') {
                    $newAirType = 'qac-1';
                } elseif ($request->air_type == 'qac-1') {
                    $newAirType = 'qac-2';
                } elseif ($request->air_type == 'qac-2') {
                    $newAirType = 'cqat';
                }
                //                elseif ($request->air_type == 'cqat'){
                //                    $newAirType = 'final';
                //                }

                $rAirData = RAir::where('id', $request->r_air_id)->first()->toArray();
                $airData = [
                    'report_type' => 'cloned',
                    'report_name' => $rAirData['report_name'],
                    'parent_id' => $rAirData['id'],
                    'fiscal_year_id' => $rAirData['fiscal_year_id'],
                    'annual_plan_id' => $rAirData['annual_plan_id'],
                    'audit_plan_id' => $rAirData['audit_plan_id'],
                    'activity_id' => $rAirData['activity_id'],
                    'ministry_id' => $rAirData['ministry_id'],
                    'ministry_name_en' => $rAirData['ministry_name_en'],
                    'ministry_name_bn' => $rAirData['ministry_name_bn'],
                    'entity_id' => $rAirData['entity_id'],
                    'entity_name_en' => $rAirData['entity_name_en'],
                    'entity_name_bn' => $rAirData['entity_name_bn'],
                    'air_description' => $rAirData['air_description'],
                    'type' => $newAirType,
                    'status' => 'draft',
                    'created_by' => $cdesk->officer_id,
                    'modified_by' => $cdesk->officer_id,
                ];
                $storeQACAir = RAir::create($airData);

                //for map data
                $apottiRAirMap = ApottiRAirMap::where('rairs_id', $request->r_air_id)->get()->toArray();
                $mappingData = [];
                foreach ($apottiRAirMap as $apotti) {
                    array_push($mappingData, [
                        'apotti_id' => $apotti['apotti_id'],
                        'rairs_id' => $storeQACAir->id
                    ]);
                }
                if (!empty($mappingData)) {
                    ApottiRAirMap::insert($mappingData);
                }
            }
            return ['status' => 'success', 'data' => ['apottis' => $request->apottis]];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAirLastMovement(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $lastAirMovementInfo = RAirMovement::where('r_air_id', $request->r_air_id)
                ->latest()
                ->first()
                ->toArray();

            return ['status' => 'success', 'data' => $lastAirMovementInfo];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAuditPlanAndTypeWiseAir(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            if ($request->qac_type == 'qac-1') {
                $newAirType = 'preliminary';
            } elseif ($request->qac_type == 'qac-2') {
                $newAirType = 'qac-1';
            } elseif ($request->qac_type == 'cqat') {
                $newAirType = 'qac-2';
            }

            $airList = RAir::select('id', 'report_name', 'fiscal_year_id', 'audit_plan_id')
                ->where('audit_plan_id', $request->audit_plan_id)
                ->where('type', $newAirType)
                ->where('status', 'approved')
                ->get()
                ->toArray();

            return ['status' => 'success', 'data' => $airList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAuditFinalReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $activity_id = $request->activity_id;
            $is_printing_done = $request->is_printing_done;

            $query = RAir::query();
            $query->with(['reported_apotti_attachments']);

            $query->when($activity_id, function ($q, $activity_id) {
                return $q->where('activity_id', $activity_id);
            });

            $query->when($is_printing_done, function ($q, $is_printing_done) {
                return $q->where('is_printing_done', $is_printing_done);
            });

            $airList =  $query->with('latest_r_air_movement')
                ->where('type', 'cqat')
                ->where('status', 'approved')
                ->get()
                ->toArray();

            return ['status' => 'success', 'data' => $airList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAuditFinalReportSearch(Request $request): array
    {
        $office_id = $request->directorate_id;

        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $fiscal_year_id = $request->fiscal_year_id;
            $ministry_id = $request->ministry_id;
            $entity_id = $request->entity_id;

            $query = RAir::query();
            $query->with(['reported_apotti_cover_page:id,report_id,cover_page_name,attachment_path']);

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($ministry_id, function ($q, $ministry_id) {
                return $q->where('ministry_id', $ministry_id);
            });

            $query->when($entity_id, function ($q, $entity_id) {
                return $q->where('entity_id', $entity_id);
            });

            $airList =  $query->where('type', 'cqat')
                ->where('status', 'approved')
                ->get()
                ->toArray();

            return ['status' => 'success', 'data' => $airList];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAuditFinalReportDetails(Request $request): array
    {
        $office_id = $request->office_id;

        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $qacApottis = ApottiRAirMap::where('rairs_id', $request->air_id)
                ->where('is_delete', 0)->pluck('apotti_id');

            $data['apotti_list'] = ApottiStatus::with('apotti')->whereIn('apotti_id', $qacApottis)
                ->where('qac_type', 'cqat')
                ->where('apotti_type', 'approved')
                ->get()
                ->toArray();

            $data['r_air'] = RAir::with(['fiscal_year','reported_apotti_attachments'])
                ->where('id', $request->air_id)
                ->first()
                ->toArray();

            return ['status' => 'success', 'data' => $data];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getArchiveFinalReport(Request $request): array
    {
        $office_id = $request->directorate_id;

        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $reports = RAir::with(['fiscal_year','reported_apotti_attachments'])
                ->where('has_report_attachments', 1)
                ->get()
                ->toArray();

            return ['status' => 'success', 'data' => $reports];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getArchiveFinalReportApotti(Request $request): array
    {
        $office_id = $request->directorate_id;

        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $apottis = Apotti::where('is_archived_reported_apotti',1)->get()->toArray();
            return ['status' => 'success', 'data' => $apottis];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function mapArchiveFinalReportApotti(Request $request): array
    {
        $office_id = $request->directorate_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $cdesk = json_decode($request->cdesk, false);

            $apotti_r_air_map = [];
            $apotii_status = [];
            foreach ($request->apottis as $apotti){
                $apotti_r_air_map[] = [
                    'apotti_id' => $apotti,
                    'rairs_id' => $request->r_air_id,
                    'created_by' => $cdesk->officer_id,
                ];

                $apotii_status[] = [
                    'apotti_id' => $apotti,
                    'apotti_type' => 'approved',
                    'qac_type' => 'cqat',
                    'created_by' => $cdesk->officer_id,
                    'created_by_name_en' =>  $cdesk->officer_en,
                    'created_by_name_bn' =>  $cdesk->officer_bn,
                ];
            }
            ApottiRAirMap::insert($apotti_r_air_map);
            ApottiStatus::insert($apotii_status);

            return ['status' => 'success', 'data' => 'Stored successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function deleteAirReportWiseApotti(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            Apotti::where('id', $request->apotti_id)->update(['is_delete' => $request->is_delete]);

            ApottiRAirMap::where('apotti_id', $request->apotti_id)
                ->where('rairs_id', $request->air_report_id)
                ->update(['is_delete' => $request->is_delete]);

            return ['status' => 'success', 'data' => []];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function apottiFinalApproval(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            Apotti::where('id', $request->apotti_id)->update(['apotti_type' => $request->final_status]);

            $apotti_status = new ApottiStatus();
            $apotti_status->apotti_id = $request->apotti_id;
            $apotti_status->qac_type = $request->qac_type;
            $apotti_status->apotti_type = $request->final_status;
            $apotti_status->created_by = $cdesk->officer_id;
            $apotti_status->created_by_name_en = $cdesk->officer_en;
            $apotti_status->created_by_name_bn = $cdesk->officer_bn;
            $apotti_status->save();

            return ['status' => 'success', 'data' => 'Approved For Final Report'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function finalReportMovement(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {

            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            //air movement data
            $airMovementData = [
                'r_air_id' => $request->r_air_id,
                'receiver_officer_id' => $request->receiver_officer_id,
                'receiver_office_id' => $request->receiver_office_id,
                'receiver_unit_id' => $request->receiver_unit_id,
                'receiver_unit_name_en' => $request->receiver_unit_name_en,
                'receiver_unit_name_bn' => $request->receiver_unit_name_bn,
                'receiver_employee_id' => $request->receiver_employee_id,
                'receiver_employee_name_en' => $request->receiver_employee_name_en,
                'receiver_employee_name_bn' => $request->receiver_employee_name_bn,
                'receiver_employee_designation_id' => $request->receiver_employee_designation_id,
                'receiver_employee_designation_en' => $request->receiver_employee_designation_en,
                'receiver_employee_designation_bn' => $request->receiver_employee_designation_bn,
                'receiver_officer_phone' => $request->receiver_officer_phone,
                'receiver_officer_email' => $request->receiver_officer_email,
                'sender_officer_id' => $cdesk->officer_id,
                'sender_office_id' => $cdesk->office_id,
                'sender_unit_id' => $cdesk->office_unit_id,
                'sender_unit_name_en' => $cdesk->office_unit_en,
                'sender_unit_name_bn' => $cdesk->office_unit_bn,
                'sender_employee_id' => $cdesk->officer_id,
                'sender_employee_name_en' => $cdesk->officer_en,
                'sender_employee_name_bn' => $cdesk->officer_bn,
                'sender_employee_designation_id' => $cdesk->designation_id,
                'sender_employee_designation_en' => $cdesk->designation_en,
                'sender_employee_designation_bn' => $cdesk->designation_bn,
                'sender_officer_phone' => $cdesk->phone,
                'sender_officer_email' => $cdesk->email,
                'comments' => $request->comments
            ];
            RAirMovement::create($airMovementData);
            return ['status' => 'success', 'data' => ['Movement Successfully']];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getAuthorityAirReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $activity_id = $request->activity_id;

            $query = RAir::query();

            $query->when($activity_id, function ($q, $activity_id) {
                return $q->where('activity_id', $activity_id);
            });

            $airList =  $query->with('latest_r_air_movement')
                ->where('type', $request->qac_type)
                ->where('status', 'approved')
                ->get()
                ->toArray();

            return ['status' => 'success', 'data' => $airList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
