<?php

namespace App\Services;

use App\Models\AuditVisitCalenderPlanMember;
use App\Models\AcQuery;
use App\Models\Query;
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
            $fiscal_year_id = $request->fiscal_year_id;
            $cost_center_id = $request->cost_center_id;

            $schedule_list = AuditVisitCalenderPlanMember::where('fiscal_year_id',$request->fiscal_year_id)->whereHas('office_order', function($q){
                                 $q->where('approve_status','approved');
                            })->with('office_order:id,audit_plan_id')->where('team_member_designation_id', $cdesk->designation_id)->paginate(PER_PAGE_PAGINATION);

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
        try {
            $fiscal_year_id = $request->fiscal_year_id;
            $cost_center_id = $request->cost_center_id;
            $queries = $request->queries;

            $query_info = AuditVisitCalenderPlanMember::where('fiscal_year_id',$request->fiscal_year_id)->where('team_member_designation_id',$cdesk->designation_id)->first();

            foreach ($queries as $key => $query){
                $ac_query = New AcQuery;
                $ac_query->fiscal_year_id = $fiscal_year_id;
                $ac_query->activity_id = $query_info->activity_id;
                $ac_query->audit_plan_id  = $query_info->audit_plan_id;
                $ac_query->office_order_id = $query_info->office_order->id;
                $ac_query->team_id = $query_info->team_id;
                $ac_query->cost_center_type_id = $request->cost_center_type_id;
                $ac_query->ministry_id  = $query_info->annual_plan->ministry_id;
                $ac_query->controlling_office_id  = $query_info->annual_plan->controlling_office_id;
                $ac_query->controlling_office_name_en  = $query_info->annual_plan->controlling_office_en;
                $ac_query->controlling_office_name_bn  = $query_info->annual_plan->controlling_office_bn;
                $ac_query->entity_office_id  = $query_info->annual_plan->parent_office_id;
                $ac_query->entity_office_name_en  = $query_info->annual_plan->parent_office_name_en;
                $ac_query->entity_office_name_bn  = $query_info->annual_plan->parent_office_name_bn;
                $ac_query->cost_center_id  = $request->cost_center_id;
                $ac_query->cost_center_name_bn  = $request->cost_center_name_bn;
                $ac_query->cost_center_name_en  = $request->cost_center_name_en;
                $ac_query->query_id  = $query['query_id'];
                $ac_query->query_title_en  = $query['query_title_en'];
                $ac_query->query_title_bn  = $query['query_title_bn'];
                $ac_query->is_query_sent  = 1;
                $ac_query->query_send_date  = date('Y-m-d');
                $ac_query->querier_officer_id  = $cdesk->officer_id;
                $ac_query->querier_designation_id  = $cdesk->designation_id;
                $ac_query->save();
            }
            return ['status' => 'success', 'data' => 'Send Successfully'];
        } catch (\Exception $exception) {
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
            $ac_query = AcQuery::where('query_id',$request->query_id)->where('cost_center_id',$request->cost_center_id)->where('fiscal_year_id',$request->fiscal_year_id)->first();

            $ac_query->query_document_received_date  = date('Y-m-d');
            $ac_query->query_receiver_officer_id  = $cdesk->officer_id;
            $ac_query->query_receiver_officer_name_bn  = $cdesk->officer_bn;
            $ac_query->query_receiver_officer_name_en  = $cdesk->officer_en;
            $ac_query->query_receiver_designation_id  = $cdesk->designation_id;
            $ac_query->is_query_document_received  = 1;
            $ac_query->save();

             return ['status' => 'success', 'data' => 'Received Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function auditQueryCostCenterTypeWise(Request $request){
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $query_list = Query::with('audit_query')->where('cost_center_type_id',$request->cost_center_type_id)->get();
            return ['status' => 'success', 'data' => $query_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];

        }
    }
}
