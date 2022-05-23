<?php

namespace App\Services;

use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\ApottiStatus;
use App\Models\QacCommittee;
use App\Models\QacCommitteeAirMap;
use App\Models\QacCommitteeMember;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use DB;

class QacService
{
    use GenericData, ApiHeart;

    public function qacApotti(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        DB::beginTransaction();

        try {
//            return ['status' => 'success', 'data' => $request->all()];
            ApottiStatus::updateOrCreate(
                ['apotti_id' => $request->apotti_id,'qac_type' => $request->qac_type],
                [
                    'apotti_id' => $request->apotti_id,
                    'apotti_type' => $request->apotti_type,
                    'qac_type' => $request->qac_type,
                    'is_audit_criteria' => $request->is_audit_criteria,
                    'is_5w_pera_model' => $request->is_5w_pera_model,
                    'is_apotti_evidence' => $request->is_apotti_evidence,
                    'is_same_porishisto' => $request->is_same_porishisto,
                    'is_criteria_same_as_irregularity' => $request->is_criteria_same_as_irregularity,
                    'is_rules_and_regulation' => $request->is_rules_and_regulation,
                    'is_imperfection' => $request->is_imperfection,
                    'is_risk_analysis' => $request->is_risk_analysis,
                    'is_broadsheet_response' => $request->is_broadsheet_response,
                    'comment' => $request->comment,
                    'created_by' => $cdesk->office_id,
                    'created_by_name_en' => $cdesk->officer_en,
                    'created_by_name_bn' => $cdesk->officer_bn,
                ]
            );

            Apotti::where('id',$request->apotti_id)->update(['apotti_type' => $request->apotti_type]);
            ApottiItem::where('apotti_id',$request->apotti_id)->update(['memo_type' => $request->apotti_type]);

            $entity_id = Apotti::find($request->apotti_id)->parent_office_id;

            if($request->qac_type != 'cqat'){
                $apotti_rearrange =  New ApottiRearrangeService();
                $apotti_rearrange->draftSfiRearrange($request->apotti_id,$request->apotti_type,$request->audit_plan_id,$entity_id);
                $apotti_rearrange->sfiRearrange($request->apotti_id,$request->apotti_type,$request->audit_plan_id,$entity_id);
                $apotti_rearrange->nonSfiRearrange($request->apotti_id,$request->apotti_type,$request->audit_plan_id,$entity_id);
                $apotti_rearrange->rejectSfiRearrange($request->apotti_id,$request->apotti_type,$request->audit_plan_id,$entity_id);
                $apotti_rearrange->nullApottiRearrange($request->apotti_id,$request->apotti_type,$request->audit_plan_id,$entity_id);
            }

            DB::commit();

            return ['status' => 'success', 'data' => 'Status Change Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getQacApottiStatus(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
//            return ['status' => 'error', 'data' => $request->all()];
           $apotti_status =  ApottiStatus::where('apotti_id',$request->apotti_id)->where('qac_type',$request->qac_type)->first();
            return ['status' => 'success', 'data' => $apotti_status];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function storeQacCommittee(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {

           $qac_committee =  new QacCommittee();
           $qac_committee->fiscal_year_id = $request->fiscal_year_id;
           $qac_committee->qac_type = $request->qac_type;
           $qac_committee->title_bn = $request->title;
           $qac_committee->title_en = '';
           $qac_committee->date = date('Y-m-d');
           $qac_committee->created_by = $cdesk->officer_id;
           $qac_committee->created_by_bn = $cdesk->officer_bn;
           $qac_committee->created_by_en = $cdesk->officer_en;
           $qac_committee->save();

           if($qac_committee->id){
               foreach (json_decode($request->member_info,true) as $member){
                   $qac_committee_member =  new QacCommitteeMember();
                   $qac_committee_member->fiscal_year_id = $request->fiscal_year_id;
                   $qac_committee_member->qac_type = $request->qac_type;
                   $qac_committee_member->qac_committee_id = $qac_committee->id;
                   $qac_committee_member->officer_id = $member['officer_id'];
                   $qac_committee_member->officer_bn = $member['officer_bn'];
                   $qac_committee_member->officer_en = $member['officer_en'];
                   $qac_committee_member->officer_unit_id = $member['officer_unit_id'];
                   $qac_committee_member->officer_unit_bn = $member['officer_unit_bn'];
                   $qac_committee_member->officer_unit_en = $member['officer_unit_en'];
                   $qac_committee_member->officer_designation_grade = $member['officer_designation_grade'];
                   $qac_committee_member->officer_designation_id = $member['officer_designation_id'];
                   $qac_committee_member->officer_designation_bn = $member['officer_designation_bn'];
                   $qac_committee_member->officer_designation_en = $member['officer_designation_en'];
                   $qac_committee_member->save();
               }
           }

            return ['status' => 'success', 'data' => 'QAC Committee Save Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function updateQacCommittee(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);

        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {

            $qac_committee =  QacCommittee::find($request->committee_id);
            $qac_committee->title_bn = $request->title;
            $qac_committee->title_en = '';
//            $qac_committee->update_by = $cdesk->officer_id;
//            $qac_committee->updated_by_bn = $cdesk->officer_bn;
//            $qac_committee->updated_by_en = $cdesk->officer_en;
            $qac_committee->save();

            QacCommitteeMember::where('qac_committee_id',$request->committee_id)->delete();

            if($qac_committee->id){
                foreach (json_decode($request->member_info,true) as $member){
                    $qac_committee_member =  new QacCommitteeMember();
                    $qac_committee_member->fiscal_year_id = $qac_committee->fiscal_year_id;
                    $qac_committee_member->qac_type = $qac_committee->qac_type;
                    $qac_committee_member->qac_committee_id = $qac_committee->id;
                    $qac_committee_member->officer_id = $member['officer_id'];
                    $qac_committee_member->officer_bn = $member['officer_bn'];
                    $qac_committee_member->officer_en = $member['officer_en'];
                    $qac_committee_member->officer_unit_id = $member['officer_unit_id'];
                    $qac_committee_member->officer_unit_bn = $member['officer_unit_bn'];
                    $qac_committee_member->officer_unit_en = $member['officer_unit_en'];
                    $qac_committee_member->officer_designation_grade = $member['officer_designation_grade'];
                    $qac_committee_member->officer_designation_id = $member['officer_designation_id'];
                    $qac_committee_member->officer_designation_bn = $member['officer_designation_bn'];
                    $qac_committee_member->officer_designation_en = $member['officer_designation_en'];
                    $qac_committee_member->save();
                }
            }

            return ['status' => 'success', 'data' => 'QAC Committee Updated Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getQacCommitteeList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $qac_committee_list =  QacCommittee::with('qac_committee_members')->where('fiscal_year_id',$request->fiscal_year_id)->where('qac_type',$request->qac_type)->get();
            return ['status' => 'success', 'data' => $qac_committee_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function deleteQacCommittee(Request $request): array
    {
//        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
             $committee_has_air = QacCommitteeAirMap::where('qac_committee_id',$request->committee_id)->first();
             if($committee_has_air){
                 return ['status' => 'success', 'data' => 'exist'];
             }
             QacCommittee::find($request->committee_id)->delete();
             QacCommitteeMember::where('qac_committee_id',$request->committee_id)->delete();
             return ['status' => 'success', 'data' => 'কমিটি বাতিল করা হয়েছে'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getQacCommitteeWiseMember(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $qac_member_list =  QacCommitteeMember::where('qac_committee_id',$request->qac_committee_id)
                ->orderBy('officer_designation_grade','ASC')->get();
            return ['status' => 'success', 'data' => $qac_member_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeAirWiseCommittee(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {

            $committee_air_map =  new QacCommitteeAirMap();
            $committee_air_map->fiscal_year_id = $request->fiscal_year_id;
            $committee_air_map->qac_type = $request->qac_type;
            $committee_air_map->air_report_id = $request->air_report_id;
            $committee_air_map->qac_committee_id = $request->qac_committee_id;
            $committee_air_map->created_by = $cdesk->officer_id;
            $committee_air_map->created_by_bn = $cdesk->officer_bn;
            $committee_air_map->created_by_en = $cdesk->officer_en;
            $committee_air_map->save();

            return ['status' => 'success', 'data' => 'QAC Committee Save Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getAirWiseCommittee(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {

            $committee_info =  QacCommitteeAirMap::with('committee.qac_committee_members')->where('air_report_id',$request->air_id)->first();
            return ['status' => 'success', 'data' => $committee_info];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }




}
