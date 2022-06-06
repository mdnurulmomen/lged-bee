<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\AcMemoAttachment;
use App\Models\AcMemoPorisishto;
use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ApottiMemoService
{
    use GenericData, ApiHeart;

    public function memoList(Request $request): array
    {
        //return ['status' => 'error', 'data' => $request->has_convert_to_apotti];

        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $fiscal_year_id = $request->fiscal_year_id;
            $entity_id = $request->entity_id;
            $cost_center_id = $request->cost_center_id;
            $activity_id = $request->activity_id;
            $jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $has_convert_to_apotti = $request->has_convert_to_apotti;

            $query = AcMemo::query();

            $query->when($has_convert_to_apotti, function ($q, $has_convert_to_apotti) {
                $has_convert_to_apotti = $has_convert_to_apotti == 'yes'?1:0;
                return $q->where('has_convert_to_apotti', $has_convert_to_apotti);
            });

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($entity_id, function ($q, $entity_id) {
                return $q->where('parent_office_id', $entity_id);
            });

            $query->when($cost_center_id, function ($q, $cost_center_id) {
                return $q->where('cost_center_id', $cost_center_id);
            });

            $query->when($jorito_ortho_poriman, function ($q, $jorito_ortho_poriman) {
                return $q->where('jorito_ortho_poriman', $jorito_ortho_poriman);
            });

            $query->when($activity_id, function ($q, $activity_id) {
                $q->whereHas('audit_plan', function ($q) use ($activity_id) {
                    return $q->where('activity_id', $activity_id);
                });
            });

            $memo_list['memo_list'] = $query->with(['ac_memo_attachments'])->orderBy('id','DESC')->paginate($request->per_page ?: config('bee_config.per_page_pagination'));
            $memo_list['total_memo'] = AcMemo::count('id');

            return ['status' => 'success', 'data' => $memo_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function convertMemoToApotti(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        \DB::beginTransaction();
        try {
            //memo
            $acMemo = AcMemo::where('id',$request->memo_id)->first();
            $acMemo->has_convert_to_apotti = 1;
            $acMemo->save();

            //porisistos
            $folder_name = $cdesk->office_id;
            $office_domain_prefix = $office_db_con_response['office_domain']['domain_prefix'];

            $porisistos = [];
            if (isset($request->porisisto_details)){
                foreach ($request->porisisto_details as $key=>$porisisto){
                    array_push($porisistos, array(
                            'ac_memo_id' => $request->memo_id,
                            'details' => $porisisto,
                            'sequence' => $key + 1,
                            'created_by' => $cdesk->officer_id
                        )
                    );
                }
                if (!empty($porisistos)) {
                    AcMemoPorisishto::where('ac_memo_id',$request->memo_id)->delete();
                    AcMemoPorisishto::insert($porisistos);
                }
            }


            //for attachments
            $finalAttachments = [];

            //for porisishtos
            if ($request->hasfile('porisishtos')) {
                foreach ($request->porisishtos as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = $office_domain_prefix . '_porisishto_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('memo/' . $folder_name . '/' . $fileName, File::get($file));
                    array_push($finalAttachments, array(
                            'ac_memo_id' => $request->memo_id,
                            'file_type' => 'porisishto',
                            'file_user_define_name' => $userDefineFileName,
                            'file_custom_name' => $fileName,
                            'file_path' => url('storage/memo/' . $folder_name . '/' . $fileName),
                            'file_size' => $fileSize,
                            'file_extension' => $fileExtension,
                            'sequence' => $key + 1,
                            'created_by' => $cdesk->officer_id,
                            'modified_by' => $cdesk->officer_id,
                        )
                    );
                }
            }

            //for pramanoks
            if ($request->hasfile('pramanoks')) {
                foreach ($request->pramanoks as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = $office_domain_prefix . '_pramanok_' . uniqid() . '.' . $file->extension();

                    Storage::disk('public')->put('memo/' . $folder_name . '/' . $fileName, File::get($file));

                    array_push($finalAttachments, array(
                            'ac_memo_id' => $request->memo_id,
                            'file_type' => 'pramanok',
                            'file_user_define_name' => $userDefineFileName,
                            'file_custom_name' => $fileName,
                            'file_path' => url('storage/memo/' . $folder_name . '/' . $fileName),
                            'file_size' => $fileSize,
                            'file_extension' => $fileExtension,
                            'sequence' => $key + 1,
                            'created_by' => $cdesk->officer_id,
                            'modified_by' => $cdesk->officer_id,
                        )
                    );
                }
            }
            AcMemoAttachment::insert($finalAttachments);

            $apotti_sequence = Apotti::where('fiscal_year_id', $acMemo->fiscal_year_id)
                ->where('parent_office_id', $acMemo->parent_office_id)
                ->max('apotti_sequence');

            $apotti = new Apotti();
            $apotti->audit_plan_id = $acMemo->audit_plan_id;
            $apotti->onucched_no = $apotti_sequence + 1;
            $apotti->apotti_title = $request->memo_title_bn;
            $apotti->apotti_description = $request->memo_description_bn;
            $apotti->ministry_id = $acMemo->ministry_id;
            $apotti->ministry_name_en = $acMemo->ministry_name_en;
            $apotti->ministry_name_bn = $acMemo->ministry_name_en;
            $apotti->parent_office_id = $acMemo->parent_office_id;
            $apotti->parent_office_name_en = $acMemo->parent_office_name_en;
            $apotti->parent_office_name_bn = $acMemo->parent_office_name_bn;
            $apotti->fiscal_year_id = $acMemo->fiscal_year_id;
            $apotti->response_of_rpu = $request->response_of_rpu;
            $apotti->irregularity_cause = $request->irregularity_cause;
            $apotti->audit_conclusion = $request->audit_conclusion;
            $apotti->audit_recommendation = $request->audit_recommendation;
            $apotti->total_jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $apotti->rpu_acceptor_officer_name_bn = $request->rpu_acceptor_officer_name_bn;
            $apotti->rpu_acceptor_designation_name_bn = $request->rpu_acceptor_designation_name_bn;
            $apotti->created_by = $cdesk->officer_id;
            $apotti->approve_status = 1;
            $apotti->status = 0;
            $apotti->apotti_sequence = $apotti_sequence + 1;
            $apotti->is_combined = 0;
            $apotti->save();

            $apotti_item = new ApottiItem();
            $apotti_item->apotti_id = $apotti->id;
            $apotti_item->memo_id = $request->memo_id;
            $apotti_item->onucched_no = $apotti_sequence + 1;
            $apotti_item->memo_irregularity_type = $request->memo_irregularity_type;
            $apotti_item->memo_irregularity_sub_type = $request->memo_irregularity_sub_type;
            $apotti_item->ministry_id = $acMemo->ministry_id;
            $apotti_item->ministry_name_en = $acMemo->ministry_name_en;
            $apotti_item->ministry_name_bn = $acMemo->ministry_name_en;
            $apotti_item->parent_office_id = $acMemo->parent_office_id;
            $apotti_item->parent_office_name_en = $acMemo->parent_office_name_en;
            $apotti_item->parent_office_name_bn = $acMemo->parent_office_name_bn;
            $apotti_item->cost_center_id = $acMemo->cost_center_id;
            $apotti_item->cost_center_name_en = $acMemo->cost_center_name_en;
            $apotti_item->cost_center_name_bn = $acMemo->cost_center_name_bn;
            $apotti_item->fiscal_year_id = $acMemo->fiscal_year_id;
            $apotti_item->audit_year_start = $acMemo->audit_year_start;
            $apotti_item->audit_year_end = $acMemo->audit_year_end;
            $apotti_item->ac_query_potro_no = $acMemo->ac_query_potro_no;
            $apotti_item->ap_office_order_id = $acMemo->ap_office_order_id;
            $apotti_item->audit_plan_id = $acMemo->audit_plan_id;
            $apotti_item->audit_type = $acMemo->audit_type;
            $apotti_item->team_id = $acMemo->team_id;
            $apotti_item->memo_title_bn = $request->memo_title_bn;
            $apotti_item->memo_description_bn = $request->memo_description_bn;
            $apotti_item->memo_title_bn = $request->memo_title_bn;
            $apotti_item->memo_type = $acMemo->memo_type;
            $apotti_item->memo_status = $acMemo->memo_status;
            $apotti_item->response_of_rpu = $request->response_of_rpu;
            $apotti_item->irregularity_cause = $request->irregularity_cause;
            $apotti_item->audit_conclusion = $request->audit_conclusion;
            $apotti_item->audit_recommendation = $request->audit_recommendation;
            $apotti_item->jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $apotti_item->created_by = $cdesk->officer_id;
            $apotti_item->status = 0;
            $apotti_item->save();

            \DB::commit();
            return ['status' => 'success', 'data' => 'Save Successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
