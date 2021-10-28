<?php

namespace App\Services;

use App\Models\AcQuery;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\Query;
use App\Models\XFiscalYear;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class AuditExecutionQueryService
{
    use GenericData, ApiHeart;

    public function auditQueryScheduleList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $fiscal_year_id = XFiscalYear::select('id')->where('start', date("Y"))->first();
            $cost_center_id = $request->cost_center_id;

            $schedule_list = AuditVisitCalenderPlanMember::where('fiscal_year_id', $fiscal_year_id->id)->whereHas('office_order', function ($q) {
                $q->where('approve_status', 'approved');
            })->with('office_order:id,audit_plan_id')->with('cost_center_type:id,cost_center_id,cost_center_type_id')->where('team_member_designation_id', $cdesk->designation_id)->where('cost_center_id', '!=', 0)->paginate(config('bee_config.per_page_pagination'));

            return ['status' => 'success', 'data' => $schedule_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function sendAuditQuery(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {

            $fiscal_year_id = $request->fiscal_year_id;
            $cost_center_id = $request->cost_center_id;
            $queries = $request->queries;

            $query_info = AuditVisitCalenderPlanMember::where('fiscal_year_id', $request->fiscal_year_id)->where('team_member_designation_id', $cdesk->designation_id)->first();
            $team_leader_info = AuditVisitCalendarPlanTeam::where('fiscal_year_id', $request->fiscal_year_id)->where('id', $query_info->team_id)->first();
            $send_rpu = [];
            foreach ($queries as $key => $query) {
                $ac_query = new AcQuery;
                $ac_query->fiscal_year_id = $fiscal_year_id;
                $ac_query->activity_id = $query_info->activity_id;
                $ac_query->audit_plan_id = $query_info->audit_plan_id;
                $ac_query->office_order_id = $query_info->office_order->id;
                $ac_query->team_id = $query_info->team_id;
                $ac_query->team_leader_name_en = $team_leader_info->leader_name_en;
                $ac_query->team_leader_name_bn = $team_leader_info->leader_name_bn;
                $ac_query->cost_center_type_id = $request->cost_center_type_id;
                $ac_query->ministry_id = $query_info->annual_plan->ministry_id;
                $ac_query->controlling_office_id = $query_info->annual_plan->controlling_office_id;
                $ac_query->controlling_office_name_en = $query_info->annual_plan->controlling_office_en;
                $ac_query->controlling_office_name_bn = $query_info->annual_plan->controlling_office_bn;
                $ac_query->entity_office_id = $query_info->annual_plan->parent_office_id;
                $ac_query->entity_office_name_en = $query_info->annual_plan->parent_office_name_en;
                $ac_query->entity_office_name_bn = $query_info->annual_plan->parent_office_name_bn;
                $ac_query->cost_center_id = $request->cost_center_id;
                $ac_query->cost_center_name_bn = $request->cost_center_name_bn;
                $ac_query->cost_center_name_en = $request->cost_center_name_en;
                $ac_query->query_id = $query['query_id'];
                $ac_query->potro_no = '1';
                $ac_query->query_title_en = $query['query_title_en'];
                $ac_query->query_title_bn = $query['query_title_bn'];
                $ac_query->is_query_sent = 1;
                $ac_query->query_send_date = date('Y-m-d');
                $ac_query->querier_officer_id = $cdesk->officer_id;
                $ac_query->querier_officer_name_en = $cdesk->officer_en;
                $ac_query->querier_officer_name_bn = $cdesk->officer_bn;
                $ac_query->querier_designation_id = $cdesk->designation_id;
                $ac_query->status = 'pending';
                $send_rpu[] = $ac_query;
                $ac_query->save();
            }

            $data = [];
            $data['query_list'] = $send_rpu;

            $send_audit_query_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.send_query_to_rpu'), $data)->json();

            if ($send_audit_query_to_rpu['status'] == 'success') {
                \DB::commit();
                return ['status' => 'success', 'data' => 'Send Successfully'];
            } else {
                throw new \Exception(json_encode($send_audit_query_to_rpu));
            }
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function receivedAuditQuery(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

//        return ['status' => 'success', 'data' => $cdesk];
        try {
            $ac_query = AcQuery::where('query_id', $request->query_id)->where('cost_center_id', $request->cost_center_id)->where('fiscal_year_id', $request->fiscal_year_id)->first();

            $ac_query->query_document_received_date = date('Y-m-d');
            $ac_query->query_receiver_officer_id = $cdesk->officer_id;
            $ac_query->query_receiver_officer_name_bn = $cdesk->officer_bn;
            $ac_query->query_receiver_officer_name_en = $cdesk->officer_en;
            $ac_query->query_receiver_designation_id = $cdesk->designation_id;
            $ac_query->is_query_document_received = 1;
            $ac_query->status = 'received';
            $ac_query->save();

            return ['status' => 'success', 'data' => 'Received Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function auditQueryCostCenterTypeWise(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $query_list = Query::with('audit_query')
                ->where('cost_center_type_id', $request->cost_center_type_id)
                ->get();
            return ['status' => 'success', 'data' => $query_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];

        }
    }

    public function rejectedAuditQuery(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ac_query = AcQuery::find($request->ac_query_id);
            $ac_query->query_rejector_officer_id = $cdesk->officer_id;
            $ac_query->query_rejector_officer_name_en = $cdesk->officer_en;
            $ac_query->query_rejector_officer_name_bn = $cdesk->officer_bn;
            $ac_query->query_rejector_officer_designation_id = $cdesk->designation_id;
            $ac_query->comment = $request->comment;
            $ac_query->status = 'removed';
            $ac_query->save();

            if ($ac_query->is_query_sent) {
                $data['query_id'] = $request->ac_query_id;
                $data['query_rejector_officer_id'] = $cdesk->officer_id;
                $data['query_rejector_officer_name_en'] = $cdesk->officer_en;
                $data['query_rejector_officer_name_bn'] = $cdesk->officer_bn;
                $data['query_rejector_officer_designation_id'] = $cdesk->designation_id;
                $data['comment'] = $request->comment;
                $data['status'] = 'removed';

                $update_audit_query_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.remove_query_to_rpu'), $data)->json();
                if ($update_audit_query_to_rpu['status'] == 'success') {
                    return ['status' => 'success', 'data' => 'Remove  Successfully'];
                } else {
                    throw new \Exception(json_encode($update_audit_query_to_rpu));
                }
            }else{
                return ['status' => 'success', 'data' => 'Remove Successfully'];
            }

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
