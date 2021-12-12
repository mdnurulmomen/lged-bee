<?php

namespace App\Services;

use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class ApottiService
{
    use GenericData, ApiHeart;

    public function getApottiList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $fiscal_year_id = $request->fiscal_year_id;
//            $cost_center_id = $request->cost_center_id;
//            $team_id = $request->team_id;
//            $memo_irregularity_type= $request->memo_irregularity_type;
//            $memo_irregularity_sub_type = $request->memo_irregularity_sub_type;
//            $memo_type = $request->memo_type;
//            $memo_status = $request->memo_status;
//            $jorito_ortho_poriman = $request->jorito_ortho_poriman;
//            $audit_year_start = $request->audit_year_start;
//            $audit_year_end = $request->audit_year_end;

            $query = Apotti::query();

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

//            $query->when($cost_center_id, function ($q, $cost_center_id) {
//                return $q->where('cost_center_id', $cost_center_id);
//            });

//            $query->when($team_id, function ($q, $team_id) {
//                return $q->where('team_id', $team_id);
//            });
//
//            $query->when($memo_irregularity_type, function ($q, $memo_irregularity_type) {
//                return $q->where('memo_irregularity_type', $memo_irregularity_type);
//            });
//
//            $query->when($memo_irregularity_sub_type, function ($q, $memo_irregularity_sub_type) {
//                return $q->where('memo_irregularity_sub_type', $memo_irregularity_sub_type);
//            });
//
//            $query->when($memo_type, function ($q, $memo_type) {
//                return $q->where('memo_type', $memo_type);
//            });
//
//            $query->when($memo_status, function ($q, $memo_status) {
//                return $q->where('memo_status', $memo_status);
//            });
//
//            $query->when($jorito_ortho_poriman, function ($q, $jorito_ortho_poriman) {
//                return $q->where('jorito_ortho_poriman', $jorito_ortho_poriman);
//            });
//
//            $query->when($audit_year_start, function ($q, $audit_year_start) {
//                return $q->where('audit_year_start', $audit_year_start);
//            });
//
//            $query->when($audit_year_end, function ($q, $audit_year_end) {
//                return $q->where('audit_year_end', $audit_year_end);
//            });


            $apotti_list = $query->with(['apotti_items'])
                ->orderBy('apotti_sequence')
                ->paginate(config('bee_config.per_page_pagination'));

            return ['status' => 'success', 'data' => $apotti_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getApottiInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $apotti_info = Apotti::with(['apotti_items'])->find($request->apotti_id);
            return ['status' => 'success', 'data' => $apotti_info];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function onucchedMerge(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $apotti_list = Apotti::with(['apotti_items'])
                ->whereIn('id',$request->apotti_id)->get();
            $apotti_items = [];
            $total_jorito_ortho_poriman = 0;
            $total_onishponno_jorito_ortho_poriman = 0;
            foreach ($apotti_list as $apotti){
                $ministry_id = $apotti['ministry_id'];
                $ministry_name_en = $apotti['ministry_name_en'];
                $ministry_name_bn = $apotti['ministry_name_bn'];

                $parent_office_id = $apotti['parent_office_id'];
                $parent_office_name_bn = $apotti['parent_office_name_bn'];
                $fiscal_year_id = $apotti['fiscal_year_id'];

                $parent_office_name_en = $apotti['parent_office_name_en'];
                foreach ($apotti['apotti_items'] as $apotti_item){
                    $apotti_item_temp = [
                        'memo_id' => $apotti_item['memo_id'],
                        'memo_irregularity_type' => $apotti_item['memo_irregularity_type'],
                        'memo_irregularity_sub_type' => $apotti_item['memo_irregularity_sub_type'],
                        'ministry_id' => $apotti_item['ministry_id'],
                        'ministry_name_en' => $apotti_item['ministry_name_en'],
                        'ministry_name_bn' => $apotti_item['ministry_name_en'],
                        'parent_office_id' => $apotti_item['parent_office_id'],
                        'parent_office_name_en' => $apotti_item['parent_office_name_en'],
                        'parent_office_name_bn' => $apotti_item['parent_office_name_bn'],
                        'cost_center_id' => $apotti_item['cost_center_id'],
                        'cost_center_name_en' => $apotti_item['cost_center_name_en'],
                        'cost_center_name_bn' => $apotti_item['cost_center_name_bn'],
                        'fiscal_year_id' => $apotti_item['fiscal_year_id'],
                        'audit_year_start' => $apotti_item['audit_year_start'],
                        'audit_year_end' => $apotti_item['audit_year_end'],
                        'ac_query_potro_no' => $apotti_item['ac_query_potro_no'],
                        'ap_office_order_id' => $apotti_item['ap_office_order_id'],
                        'audit_plan_id' => $apotti_item['audit_plan_id'],
                        'audit_type' => $apotti_item['audit_type'],
                        'team_id' => $apotti_item['team_id'],
                        'memo_title_bn' => $apotti_item['memo_title_bn'],
                        'memo_description_bn' => $apotti_item['memo_description_bn'],
                        'memo_type' => $apotti_item['memo_type'],
                        'memo_status' => $apotti_item['memo_status'],
                        'jorito_ortho_poriman' => $apotti_item['jorito_ortho_poriman'],
                        'onishponno_jorito_ortho_poriman' => $apotti_item['onishponno_jorito_ortho_poriman'],
                        'response_of_rpu' => $apotti_item['response_of_rpu'],
                        'audit_conclusion' => $apotti_item['audit_conclusion'],
                        'audit_recommendation' => $apotti_item['audit_recommendation'],
                        'created_by' => $cdesk->officer_id,
                        'status' => 0,
                    ];
                    $apotti_items_info[] = $apotti_item_temp;

                    $total_jorito_ortho_poriman += $apotti_item['jorito_ortho_poriman'];
                    $total_onishponno_jorito_ortho_poriman += $apotti_item['onishponno_jorito_ortho_poriman'];
                }
            }

            $apotti_items =  $apotti_items_info;

            Apotti::whereIn('id',$request->apotti_id)->delete();
            ApottiItem::whereIn('apotti_id',$request->apotti_id)->delete();

            $apotti = new Apotti();
            $apotti->onucched_no = $request->onucched_no;
            $apotti->apotti_title = $request->apotti_title;
            $apotti->apotti_description = $request->apotti_description;
            $apotti->ministry_id = $ministry_id;
            $apotti->ministry_name_en = $ministry_name_en;
            $apotti->ministry_name_bn = $ministry_name_bn;
            $apotti->parent_office_id = $parent_office_id;
            $apotti->parent_office_name_en = $parent_office_name_en;
            $apotti->parent_office_name_bn = $parent_office_name_bn;
            $apotti->fiscal_year_id = $fiscal_year_id;
            $apotti->total_jorito_ortho_poriman = $total_jorito_ortho_poriman;
            $apotti->total_onishponno_jorito_ortho_poriman = $total_onishponno_jorito_ortho_poriman;
            $apotti->irregularity_cause = $request->irregularity_cause;
            $apotti->response_of_rpu = $request->response_of_rpu;
            $apotti->audit_conclusion = $request->audit_conclusion;
            $apotti->audit_recommendation = $request->audit_recommendation;
            $apotti->is_combined = 1;
            $apotti->save();

            foreach ($apotti_items as $apotti_item){
                   $apotti_item_save =  New ApottiItem();
                   $apotti_item_save->apotti_id = $apotti->id;
                   $apotti_item_save->memo_id = $apotti_item['memo_id'];
                   $apotti_item_save->onucched_no = 1;
                   $apotti_item_save->memo_irregularity_type = $apotti_item['memo_irregularity_type'];
                   $apotti_item_save->memo_irregularity_sub_type = $apotti_item['memo_irregularity_sub_type'];
                   $apotti_item_save->ministry_id = $apotti_item['ministry_id'];
                   $apotti_item_save->ministry_name_en = $apotti_item['ministry_name_en'];
                   $apotti_item_save->ministry_name_bn = $apotti_item['ministry_name_en'];
                   $apotti_item_save->parent_office_id = $apotti_item['parent_office_id'];
                   $apotti_item_save->parent_office_name_en = $apotti_item['parent_office_name_en'];
                   $apotti_item_save->parent_office_name_bn = $apotti_item['parent_office_name_bn'];
                   $apotti_item_save->cost_center_id = $apotti_item['cost_center_id'];
                   $apotti_item_save->cost_center_name_en = $apotti_item['cost_center_name_en'];
                   $apotti_item_save->cost_center_name_bn = $apotti_item['cost_center_name_bn'];
                   $apotti_item_save->fiscal_year_id = $apotti_item['fiscal_year_id'];
                   $apotti_item_save->audit_year_start = $apotti_item['audit_year_start'];
                   $apotti_item_save->audit_year_end = $apotti_item['audit_year_end'];
                   $apotti_item_save->ac_query_potro_no = $apotti_item['ac_query_potro_no'];
                   $apotti_item_save->ap_office_order_id = $apotti_item['ap_office_order_id'];
                   $apotti_item_save->audit_plan_id = $apotti_item['audit_plan_id'];
                   $apotti_item_save->audit_type = $apotti_item['audit_type'];
                   $apotti_item_save->team_id = $apotti_item['team_id'];
                   $apotti_item_save->memo_title_bn = $apotti_item['memo_title_bn'];
                   $apotti_item_save->memo_description_bn = $apotti_item['memo_description_bn'];
                   $apotti_item_save->memo_title_bn = $apotti_item['memo_title_bn'];
                   $apotti_item_save->memo_type = $apotti_item['memo_type'];
                   $apotti_item_save->memo_status = $apotti_item['memo_status'];
                   $apotti_item_save->jorito_ortho_poriman = $apotti_item['jorito_ortho_poriman'];
                   $apotti_item_save->onishponno_jorito_ortho_poriman = $apotti_item['onishponno_jorito_ortho_poriman'];
                   $apotti_item_save->created_by = $cdesk->officer_id;
                   $apotti_item_save->status = $apotti_item['status'];
                   $apotti_item_save->save();
            }
            \DB::commit();
            return ['status' => 'success', 'data' => 'Merge Successfully'];

        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function onucchedUnMerge(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {

            $apotti_item_info = ApottiItem::find($request->apotti_item_id);

            $amount_update = Apotti::find($apotti_item_info->apotti_id);
            $amount_update->total_jorito_ortho_poriman = $amount_update->total_jorito_ortho_poriman - $apotti_item_info->jorito_ortho_poriman;
            $amount_update->total_onishponno_jorito_ortho_poriman = $amount_update->total_onishponno_jorito_ortho_poriman - $apotti_item_info->onishponno_jorito_ortho_poriman;
            $amount_update->save();

            $apotti = new Apotti();
            $apotti->onucched_no = 1;
            $apotti->apotti_title = $apotti_item_info->memo_title_bn;
            $apotti->apotti_description = $apotti_item_info->memo_description_bn;
            $apotti->ministry_id = $apotti_item_info->ministry_id;
            $apotti->ministry_name_en = $apotti_item_info->ministry_name_en;
            $apotti->ministry_name_bn = $apotti_item_info->ministry_name_en;
            $apotti->parent_office_id = $apotti_item_info->parent_office_id;
            $apotti->parent_office_name_en = $apotti_item_info->parent_office_name_en;
            $apotti->parent_office_name_bn = $apotti_item_info->parent_office_name_bn;
            $apotti->fiscal_year_id = $apotti_item_info->fiscal_year_id;
            $apotti->total_jorito_ortho_poriman = $apotti_item_info->jorito_ortho_poriman;
            $apotti->total_onishponno_jorito_ortho_poriman = $apotti_item_info->onishponno_jorito_ortho_poriman;
            $apotti->created_by = $cdesk->officer_id;
            $apotti->approve_status = 1;
            $apotti->status = 0;
            $apotti->apotti_sequence = 0;
            $apotti->is_combined = 0;
            $apotti->save();

            $apotti_item = new ApottiItem();
            $apotti_item->apotti_id = $apotti->id;
            $apotti_item->memo_id = $apotti_item_info->memo_id;
            $apotti_item->onucched_no = 1;
            $apotti_item->memo_irregularity_type = $apotti_item_info->memo_irregularity_type;
            $apotti_item->memo_irregularity_sub_type = $apotti_item_info->memo_irregularity_sub_type;
            $apotti_item->ministry_id = $apotti_item_info->ministry_id;
            $apotti_item->ministry_name_en = $apotti_item_info->ministry_name_en;
            $apotti_item->ministry_name_bn = $apotti_item_info->ministry_name_en;
            $apotti_item->parent_office_id = $apotti_item_info->parent_office_id;
            $apotti_item->parent_office_name_en = $apotti_item_info->parent_office_name_en;
            $apotti_item->parent_office_name_bn = $apotti_item_info->parent_office_name_bn;
            $apotti_item->cost_center_id = $apotti_item_info->cost_center_id;
            $apotti_item->cost_center_name_en = $apotti_item_info->cost_center_name_en;
            $apotti_item->cost_center_name_bn = $apotti_item_info->cost_center_name_bn;
            $apotti_item->fiscal_year_id = $apotti_item_info->fiscal_year_id;
            $apotti_item->audit_year_start = $apotti_item_info->audit_year_start;
            $apotti_item->audit_year_end = $apotti_item_info->audit_year_end;
            $apotti_item->ac_query_potro_no = $apotti_item_info->ac_query_potro_no;
            $apotti_item->ap_office_order_id = $apotti_item_info->ap_office_order_id;
            $apotti_item->audit_plan_id = $apotti_item_info->audit_plan_id;
            $apotti_item->audit_type = $apotti_item_info->audit_type;
            $apotti_item->team_id = $apotti_item_info->team_id;
            $apotti_item->memo_title_bn = $apotti_item_info->memo_title_bn;
            $apotti_item->memo_description_bn = $apotti_item_info->memo_description_bn;
            $apotti_item->memo_title_bn = $apotti_item_info->memo_title_bn;
            $apotti_item->memo_type = $apotti_item_info->memo_type;
            $apotti_item->memo_status = $apotti_item_info->memo_status;
            $apotti_item->jorito_ortho_poriman = $apotti_item_info->jorito_ortho_poriman;
            $apotti_item->onishponno_jorito_ortho_poriman = $apotti_item_info->onishponno_jorito_ortho_poriman;
            $apotti_item->created_by = $cdesk->officer_id;
            $apotti_item->status = 0;
            $apotti_item->save();

            ApottiItem::where('id',$request->apotti_item_id)->delete();
            \DB::commit();
            return ['status' => 'success', 'data' => 'UnMerge Successfully'];

        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function onucchedReArrange(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $apotti_sequence = $request->apotti_sequence;

            foreach ($apotti_sequence as $sequence){
               $apotti =  Apotti::find($sequence['apotti_id']);
               $apotti->apotti_sequence = $sequence['apotti_sequence'];
               $apotti->save();
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'Rearrange Successfully'];

        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

}
