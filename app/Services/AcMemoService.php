<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class AcMemoService
{
    use GenericData;

    public function auditMemoStore(Request $request): array
    {
        //return ['status' => 'error', 'data' => $request->all()];

        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {

            $schedule = AuditVisitCalenderPlanMember::where('id',$request->schedule_id)->first();

            $audi_memo = New AcMemo();
            $audi_memo->onucched_no = '1';
            $audi_memo->memo_irregularity_type = $request->memo_irregularity_type;
            $audi_memo->memo_irregularity_sub_type = $request->memo_irregularity_sub_type;
            $audi_memo->ministry_id = $schedule->plan_team->ministry_id;
            $audi_memo->ministry_name_en = 'Ministry Name';
            $audi_memo->controlling_office_id = $schedule->plan_team->controlling_office_id;
            $audi_memo->controlling_office_name_en = $schedule->plan_team->controlling_office_name_en;
            $audi_memo->controlling_office_name_bn = $schedule->plan_team->controlling_office_name_bn;
            $audi_memo->parent_office_id = $schedule->plan_team->entity_id;
            $audi_memo->parent_office_name_en = $schedule->plan_team->entity_name_en;
            $audi_memo->parent_office_name_bn = $schedule->Plan_team->entity_name_bn;
            $audi_memo->cost_center_id = $schedule->cost_center_id;
            $audi_memo->cost_center_name_en = $schedule->cost_center_name_bn;
            $audi_memo->cost_center_name_bn = $schedule->cost_center_name_bn;
            $audi_memo->fiscal_year_id = $schedule->fiscal_year_id;
            $audi_memo->audit_plan_id = $schedule->audit_plan_id;
            $audi_memo->audit_year_start = $request->audit_year_start;
            $audi_memo->audit_year_end = $request->audit_year_end;
            $audi_memo->ac_query_potro_no = 1; //to do
            $audi_memo->audit_type = '1';
            $audi_memo->team_id = $schedule->team_id;
            $audi_memo->memo_title_bn = $request->memo_title_bn;
            $audi_memo->memo_description_bn = $request->memo_description_bn;
            $audi_memo->memo_type = $request->memo_type;
            $audi_memo->memo_status = $request->memo_status;
            $audi_memo->jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $audi_memo->onishponno_jorito_ortho_poriman = $request->onishponno_jorito_ortho_poriman;
            $audi_memo->response_of_rpu = $request->response_of_rpu;
            $audi_memo->audit_conclusion = $request->audit_conclusion;
            $audi_memo->audit_recommendation = $request->audit_recommendation;
            $audi_memo->created_by = $cdesk->officer_id;
            $audi_memo->approve_status = 'draft';
            $audi_memo->status = 'draft';
            $audi_memo->comment = '';
            $audi_memo->save();

            return ['status' => 'success', 'data' => 'Memo Saved Successfully'];

//            if ($send_audit_query_to_rpu['status'] == 'success') {
//                \DB::commit();
//                return ['status' => 'success', 'data' => 'Send Successfully'];
//            } else {
//                throw new \Exception(json_encode($send_audit_query_to_rpu));
//            }
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $memo_list = AcMemo::where('audit_plan_id', $request->audit_plan_id)->where('cost_center_id', $request->cost_center_id)->paginate(config('bee_config.per_page_pagination'));
            return ['status' => 'error', 'data' => $memo_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
