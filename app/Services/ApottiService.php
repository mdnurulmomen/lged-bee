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
            $apotti_list = Apotti::with(['apotti_items'])
                ->paginate(config('bee_config.per_page_pagination'));
            return ['status' => 'success', 'data' => $apotti_list];
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
        try {
            $apotti_list = Apotti::with(['apotti_items'])
                ->whereIn('id',$request->apotti_id)
                ->paginate(config('bee_config.per_page_pagination'));

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
            $apotti->onucched_no = 1;
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
            $apotti->save();

            foreach ($apotti_items as $apotti_item){
                   $apotti_item_save =  New ApottiItem();
                   $apotti_item_save->apotti_id = $apotti->id;
                   $apotti_item_save->memo_id = $apotti_item['memo_id'];
                   $apotti_item_save->onucched_no = 1;
                   $apotti_item_save->memo_irregularity_type = $apotti_item['memo_irregularity_type'];
                   $apotti_item_save->memo_irregularity_sub_type = $apotti_item['memo_irregularity_sub_type'];
                   $apotti_item_save->cost_center_id = $apotti_item['memo_irregularity_sub_type'];
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

           return ['status' => 'success', 'data' => 'Merge Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
