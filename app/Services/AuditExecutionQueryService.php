<?php

namespace App\Services;

use App\Models\AcQuery;
use App\Models\AcQueryItem;
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
            $fiscal_year_id = $request->fiscal_year_id;
            $activity_id = $request->activity_id;
            $audit_plan_id = $request->audit_plan_id;

            $query = AuditVisitCalenderPlanMember::query();

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($activity_id, function ($q, $activity_id) {
                return $q->where('activity_id', $activity_id);
            });

            $query->when($audit_plan_id, function ($q, $audit_plan_id) {
                return $q->where('audit_plan_id', $audit_plan_id);
            });

            $schedule_list =  $query->with('annual_plan:id,project_id,project_name_bn,project_name_en')
                ->with('plan_parent_team:id,team_parent_id,team_name,team_start_date,team_end_date,leader_name_en,leader_name_bn,leader_designation_name_en,leader_designation_name_bn,audit_year_start,audit_year_end')
                ->with('plan_team:id,team_parent_id,team_name,team_start_date,team_end_date,leader_name_en,leader_name_bn,leader_designation_name_en,leader_designation_name_bn,audit_year_start,audit_year_end,team_members')
                ->with('office_order:id,audit_plan_id,approved_status')
                ->withCount(['queries','memos'])
                ->where('audit_plan_id', '!=', 0)
                ->where('team_member_officer_id', $cdesk->officer_id)
                ->whereNotNull('cost_center_id')
                ->orderBy('team_member_start_date', 'ASC')
                ->paginate($request->per_page ?: config('bee_config.per_page_pagination'));

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
            //todo
            $acQuery = AcQuery::where('id', $request->ac_query_id)
                ->with('plan_team:id,team_name,team_parent_id,leader_name_en,leader_name_bn,leader_designation_id,leader_designation_name_en,leader_designation_name_bn')
                ->first();

            $fiscal_year_info = XFiscalYear::select('start', 'end')->find($acQuery->fiscal_year_id);

            $acQueryItems = AcQueryItem::select('id', 'ac_query_id', 'item_title_en', 'item_title_bn', 'status')
                ->where('ac_query_id', $request->ac_query_id)
                ->get();

            $data = [];
            $data['ac_query'] = $acQuery;
            $data['ac_query_items'] = json_encode_unicode($acQueryItems);
            $data['directorate_id'] = $cdesk->office_id;
            $data['directorate_en'] = $cdesk->office_name_en;
            $data['directorate_bn'] = $cdesk->office_name_bn;
            $data['fiscal_year'] = $fiscal_year_info->start . '-' . $fiscal_year_info->end;

            $send_audit_query_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.send_query_to_rpu'), $data)->json();

            if ($send_audit_query_to_rpu['status'] == 'success') {
                $acQuery->has_sent_to_rpu = 1;
                $acQuery->status = 'sent';
                $acQuery->save();

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

        try {
            $acQueryItemUpdate = AcQueryItem::find($request->ac_query_item_id);
            $acQueryItemUpdate->status = 'received';
            $acQueryItemUpdate->receiver_officer_id = $cdesk->officer_id;
            $acQueryItemUpdate->receiver_officer_name_bn = $cdesk->officer_bn;
            $acQueryItemUpdate->receiver_officer_name_en = $cdesk->officer_en;
            $acQueryItemUpdate->receiver_unit_name_bn = $cdesk->office_unit_bn;
            $acQueryItemUpdate->receiver_unit_name_en = $cdesk->office_unit_en;
            $acQueryItemUpdate->receiver_designation_name_bn = $cdesk->designation_bn;
            $acQueryItemUpdate->receiver_designation_name_en = $cdesk->designation_en;
            $acQueryItemUpdate->save();

            $acQueryItems = AcQueryItem::select('ac_query_id', 'item_title_en', 'item_title_bn', 'status')
                ->where('ac_query_id', $request->ac_query_id)
                ->get();

            $data['ac_query_id'] = $request->ac_query_id;
            $data['ac_query_item_id'] = $request->ac_query_item_id;
            $data['ac_query_items'] = json_encode_unicode($acQueryItems);

            $received_query = $this->initRPUHttp()->post(config('cag_rpu_api.received_query_from_rpu'), $data)->json();
            if ($received_query['status'] == 'success') {
                return ['status' => 'success', 'data' => 'Received Successfully'];
            } else {
                return ['status' => 'error', 'data' => $received_query];
                //throw new \Exception(json_encode($received_query));
            }

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function loadAuditQuery(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ac_query_list = AcQuery::where('audit_plan_id', $request->audit_plan_id)
                ->where('entity_office_id', $request->entity_id)
                ->where('cost_center_id', $request->cost_center_id)
                ->get();
            return ['status' => 'success', 'data' => $ac_query_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];

        }
    }

    public function loadTypeWiseAuditQuery(Request $request)
    {
        try {
            $query_list = Query::where('cost_center_type_id', $request->cost_center_type_id)->get();
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
                $data['query_id'] = $ac_query->query_id;
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
            } else {
                return ['status' => 'success', 'data' => 'Remove Successfully'];
            }

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function rpuSendQueryList(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ac_query_list = AcQuery::where('cost_center_type_id', $request->cost_center_type_id)
                ->where('cost_center_id', $request->cost_center_id)
                ->where('is_query_sent', 1)
                ->where('status', '!=', 'removed')
                ->get();
            return ['status' => 'success', 'data' => $ac_query_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeAuditQuery(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        \DB::beginTransaction();
        try {
            $schedule = AuditVisitCalenderPlanMember::with('plan_team:id')
                ->with('office_order:id,audit_plan_id')
                ->where('id', $request->schedule_id)
                ->first();

            $ac_query = new AcQuery();
            $ac_query->fiscal_year_id = $schedule->fiscal_year_id;
            $ac_query->activity_id = $schedule->activity_id;
            $ac_query->annual_plan_id = $schedule->annual_plan_id;
            $ac_query->audit_plan_id = $schedule->audit_plan_id;
            $ac_query->office_order_id = $schedule->office_order->id;
            $ac_query->team_id = $schedule->team_id;
            $ac_query->entity_office_id = $schedule->entity_id;
            $ac_query->entity_office_name_en = $schedule->entity_name_en;
            $ac_query->entity_office_name_bn = $schedule->entity_name_bn;
            $ac_query->cost_center_id = $schedule->cost_center_id;
            $ac_query->cost_center_name_en = $schedule->cost_center_name_en;
            $ac_query->cost_center_name_bn = $schedule->cost_center_name_bn;

            $ac_query->querier_officer_id = $cdesk->officer_id;
            $ac_query->querier_officer_name_en = $cdesk->officer_en;
            $ac_query->querier_officer_name_bn = $cdesk->officer_bn;
            $ac_query->querier_unit_name_en = $cdesk->office_unit_en;
            $ac_query->querier_unit_name_bn = $cdesk->office_unit_bn;
            $ac_query->querier_designation_id = $cdesk->designation_id;
            $ac_query->querier_designation_bn = $cdesk->designation_bn;
            $ac_query->querier_designation_en = $cdesk->designation_en;

            $ac_query->issued_by = $request->issued_by;
            $ac_query->team_leader_name = $request->issued_by == 'team_leader' ? $request->team_leader_name : $request->sub_team_leader_name;
            $ac_query->team_leader_designation = $request->issued_by == 'team_leader' ? $request->team_leader_designation : $request->sub_team_leader_designation;

            $ac_query->rpu_office_head_details = $request->rpu_office_head_details;
            $ac_query->memorandum_no = $request->memorandum_no;
            $ac_query->memorandum_date = $request->memorandum_date;
            $ac_query->suthro = $request->suthro;
            $ac_query->subject = $request->subject;
            $ac_query->description = $request->description;
            $ac_query->cc = $request->cc;
            $ac_query->responsible_person_details = $request->responsible_person_details;
            $ac_query->status = 'pending';
            $ac_query->created_by = $cdesk->officer_id;
            $ac_query->save();


            //for insert items
            $acQueryItems = array();
            foreach ($request->audit_query_items as $value) {
                if (!empty($value)) {
                    $acQueryItems[] = array(
                        'ac_query_id' => $ac_query->id,
                        'item_title_en' => $value,
                        'item_title_bn' => $value,
                        'status' => 'pending'
                    );
                }
            }
            if (!empty($acQueryItems)) {
                AcQueryItem::insert($acQueryItems);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'Query Saved Successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function updateAuditQuery(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ac_query = AcQuery::find($request->ac_query_id);
            $ac_query->rpu_office_head_details = $request->rpu_office_head_details;
            $ac_query->memorandum_no = $request->memorandum_no;
            $ac_query->memorandum_date = $request->memorandum_date;
            $ac_query->suthro = $request->suthro;
            $ac_query->subject = $request->subject;
            $ac_query->description = $request->description;
            $ac_query->cc = $request->cc;

            $ac_query->issued_by = $request->issued_by;
            $ac_query->team_leader_name = $request->issued_by == 'team_leader' ? $request->team_leader_name : $request->sub_team_leader_name;
            $ac_query->team_leader_designation = $request->issued_by == 'team_leader' ? $request->team_leader_designation : $request->sub_team_leader_designation;
            $ac_query->responsible_person_details = $request->responsible_person_details;

            $ac_query->status = 'pending';
            $ac_query->updated_by = $cdesk->officer_id;
            $ac_query->save();


            //for insert items
            AcQueryItem::where('ac_query_id', $request->ac_query_id)->delete();
            $acQueryItems = array();
            foreach ($request->audit_query_items as $value) {
                if (!empty($value)) {
                    $acQueryItems[] = array(
                        'ac_query_id' => $ac_query->id,
                        'item_title_en' => $value,
                        'item_title_bn' => $value,
                        'status' => 'pending'
                    );
                }
            }
            if (!empty($acQueryItems)) {
                AcQueryItem::insert($acQueryItems);
            }

            return ['status' => 'success', 'data' => 'Query Saved Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function viewAuditQuery(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        if ($request->has('directorate_id')) {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
        } else {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
        }
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ac_query = AcQuery::with('query_items')
                ->with('plan_team:id,team_name,team_parent_id,leader_name_bn,leader_name_en,leader_designation_name_bn,leader_designation_name_en')
                ->where('id', $request->ac_query_id)
                ->first();
            return ['status' => 'success', 'data' => $ac_query];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function authorityQueryList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $fiscal_year_id = $request->fiscal_year_id;
            $cost_center_id = $request->cost_center_id;
            $entity_id = $request->entity_id;
            $activity_id = $request->activity_id;
            $team_id = $request->team_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $query = AcQuery::query();

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($activity_id, function ($q, $activity_id) {
                return $q->where('activity_id', $activity_id);
            });

            $query->when($entity_id, function ($q, $entity_id) {
                return $q->where('entity_office_id', $entity_id);
            });

            $query->when($cost_center_id, function ($q, $cost_center_id) {
                return $q->where('cost_center_id', $cost_center_id);
            });

            $query->when($team_id, function ($q, $team_id) {
                return $q->where('team_id', $team_id);
            });

            $query->when($start_date, function ($q, $start_date) {
                return $q->whereDate('created_at','>=',$start_date);
            });

            $query->when($end_date, function ($q, $end_date) {
                return $q->whereDate('created_at','<=', $end_date);
            });

            if ($request->has('status')) {
                if ($request->status == 'daily') {
                    $query->whereDate('created_at', date('Y-m-d'));
                }
            }

            $query_list = $query->get();

            return ['status' => 'success', 'data' => $query_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function responseOfRpuQuery(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            AcQueryItem::where('id', $request->query_item_id)->update(
                [
                    'status' => $request->status,
                    'comment' => $request->comment,
                ]
            );
            return ['status' => 'success', 'data' => 'Response Send Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
