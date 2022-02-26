<?php

namespace App\Services;

use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\ApottiStatus;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use DB;
class ApottiService
{
    use GenericData, ApiHeart;

    public function getApottiList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $fiscal_year_id = $request->fiscal_year_id;
            $qac_type = $request->qac_type;
            $audit_plan_id = $request->audit_plan_id;
            $entity_id = $request->entity_id;
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

            $query->when($audit_plan_id, function ($q, $audit_plan_id) {
                return $q->where('audit_plan_id', $audit_plan_id);
            });

            $query->when($entity_id, function ($q, $entity_id) {
                return $q->where('parent_office_id', $entity_id);
            });


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
            $query->when($qac_type, function ($q, $qac_type) {
                if($qac_type == 'qac-2'){
                    return $q->where('apotti_type', 'sfi');
                }
            });


            $apotti_list = $query->with(['apotti_items','apotti_status'])
                ->orderBy('onucched_no')
                ->paginate(config('bee_config.per_page_pagination'));

            return ['status' => 'success', 'data' => $apotti_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getApottiInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
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
        DB::beginTransaction();
        try {



//            return ['status' => 'error', 'data' => $request->sequence];

            $apotti_list = Apotti::with(['apotti_items'])
                ->whereIn('id',$request->apotti_id)->get();
            $apotti_items = [];
            $total_onishponno_jorito_ortho_poriman = 0;
            foreach ($apotti_list as $apotti){
                $audit_plan_id = $apotti['audit_plan_id'];
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
                    $total_onishponno_jorito_ortho_poriman += $apotti_item['onishponno_jorito_ortho_poriman'];
                }
            }

            $apotti_items =  $apotti_items_info;

//            return ['status' => 'success', 'data' => $higher_sequence];

            Apotti::whereIn('id',$request->apotti_id)->delete();
            ApottiItem::whereIn('apotti_id',$request->apotti_id)->delete();

            $apotti = new Apotti();
            $apotti->audit_plan_id = $audit_plan_id;
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
            $apotti->total_jorito_ortho_poriman = $request->total_jorito_ortho_poriman;
            $apotti->total_onishponno_jorito_ortho_poriman = $total_onishponno_jorito_ortho_poriman;
            $apotti->irregularity_cause = $request->irregularity_cause;
            $apotti->response_of_rpu = $request->response_of_rpu;
            $apotti->audit_conclusion = $request->audit_conclusion;
            $apotti->audit_recommendation = $request->audit_recommendation;
            $apotti->created_by = $cdesk->officer_id;
            $apotti->approve_status = 0;
            $apotti->apotti_sequence = $request->sequence;
            $apotti->status = 0;
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

            $higher_sequence = Apotti::where('audit_plan_id',$audit_plan_id)->where('apotti_sequence','>',$request->sequence)->pluck('id');

            $sequence = $request->sequence;

            foreach ($higher_sequence as $sequence_apotti){
                $sequence++;
//                return ['status' => 'success', 'data' => $sequence];
                Apotti::where('id',$sequence_apotti)->update(['apotti_sequence' => $sequence]);
            }

            DB::commit();
            return ['status' => 'success', 'data' => 'Merge Successfully'];

        }catch (\Error $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        } catch (\Exception $exception) {
            DB::rollback();
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
        DB::beginTransaction();
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
            DB::commit();
            return ['status' => 'success', 'data' => 'UnMerge Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
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
        DB::beginTransaction();
        try {
//            $apotti_sequence = $request->apotti_sequence;
//
//            foreach ($apotti_sequence as $sequence){
//               $apotti =  Apotti::find($sequence['apotti_id']);
//               $apotti->apotti_sequence = $sequence['apotti_sequence'];
//               $apotti->save();
//            }

            $onucched_list = $request->onucched_list;

            foreach ($onucched_list as $onucched_no){
                $apotti =  Apotti::find($onucched_no['apotti_id']);
                $apotti->onucched_no = $onucched_no['onucched_no'];
                $apotti->save();
            }

            DB::commit();
            return ['status' => 'success', 'data' => 'Rearrange Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function apottiWiseAllItem(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $apotti_list = Apotti::with(['apotti_items'])
                ->whereIn('id',$request->apottiId)->get();
            $apotti_items = [];
            foreach ($apotti_list as $apotti){
                foreach ($apotti['apotti_items'] as $apotti_item){
                    $apotti_item_temp = [
                        'apotti_item_id' => $apotti_item['id'],
                        'memo_title_bn' => $apotti_item['memo_title_bn'],
                        'jorito_ortho_poriman' => $apotti_item['jorito_ortho_poriman'],
                    ];
                    $apotti_items_info[] = $apotti_item_temp;
                }
            }

            $apotti_items =  $apotti_items_info;

            return ['status' => 'success', 'data' => $apotti_items];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getApottiItemInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $apotti_item_info = ApottiItem::find($request->apotti_item_id);
            return ['status' => 'success', 'data' => $apotti_item_info];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function updateApotti(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $apotti = Apotti::find($request->apotti_id);
            $apotti->onucched_no = $request->onucched_no;
            $apotti->apotti_title = $request->apotti_title;
            $apotti->apotti_description = $request->apotti_description;
            $apotti->irregularity_cause = $request->irregularity_cause;
            $apotti->response_of_rpu = $request->response_of_rpu;
            $apotti->audit_conclusion = $request->audit_conclusion;
            $apotti->audit_recommendation = $request->audit_recommendation;
            $apotti->save();

            return ['status' => 'success', 'data' => 'Update Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getApottiOnucchedNo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $qac_type = $request->qac_type;

            $apotti_list = Apotti::select('id','onucched_no')
                                ->with('apotti_status', function ($q) use ($qac_type){
                                    $q->select('id','apotti_id','apotti_type')->where('qac_type', $qac_type);
                                    })
                                ->where('audit_plan_id',$request->audit_plan_id)
                                ->where('parent_office_id',$request->entity_id)
                                ->get();

            return ['status' => 'success', 'data' => $apotti_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getApottiRegisterlist(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $fiscal_year_id = $request->fiscal_year_id;
            $apotti_type = $request->apotti_type;
            $audit_plan_id = $request->audit_plan_id;
            $activity_id = $request->activity_id;
            $entity_id = $request->entity_id;
//          $cost_center_id = $request->cost_center_id;

            $query = Apotti::query();

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($audit_plan_id, function ($q, $audit_plan_id) {
                return $q->where('audit_plan_id', $audit_plan_id);
            });

            $query->when($entity_id, function ($q, $entity_id) {
                return $q->where('parent_office_id', $entity_id);
            });


            $apotti_list = $query->whereHas('apotti_status', function ($q) use ($apotti_type){
                $q->select('id','apotti_id','apotti_type')
                    ->where('apotti_type', $apotti_type)
                    ->where('qac_type', 'qac-1');
            })->with('apotti_items')

                ->orderBy('onucched_no')
                ->paginate(config('bee_config.per_page_pagination'));

            return ['status' => 'success', 'data' => $apotti_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

}
