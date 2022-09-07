<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\AcMemoAttachment;
use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use DB;

class ApottiSearchService
{
    use GenericData, ApiHeart;

    public function list(Request $request)
    {
        try {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $query = ApottiItem::query();

            //ministry
            $ministry_id = $request->ministry_id;
            $query->when($ministry_id, function ($query) use ($ministry_id) {
                return $query->where('ministry_id', $ministry_id);
            });

            //entity
            $entity_id = $request->entity_id;
            $query->when($entity_id, function ($query) use ($entity_id) {
                return $query->where('parent_office_id', $entity_id);
            });

            //cost_center_id
            $cost_center_id = $request->cost_center_id;
            $query->when($cost_center_id, function ($query) use ($cost_center_id) {
                return $query->where('cost_center_id', $cost_center_id);
            });

            //onucched_no
            $onucched_no = $request->onucched_no;
            $query->when($onucched_no, function ($query) use ($onucched_no) {
                return $query->where('onucched_no', $onucched_no);
            });

            //fiscal_year_id
            $fiscal_year_id = $request->fiscal_year_id;
            $query->when($fiscal_year_id, function ($query) use ($fiscal_year_id) {
                return $query->where('fiscal_year_id', $fiscal_year_id);
            });

            //audit_year_start
            $audit_year_start = $request->audit_year_start;
            $query->when($audit_year_start, function ($query) use ($audit_year_start) {
                return $query->where('audit_year_start', $audit_year_start);
            });

            //audit_year_end
            $audit_year_end = $request->audit_year_end;
            $query->when($audit_year_end, function ($query) use ($audit_year_end) {
                return $query->where('audit_year_end', $audit_year_end);
            });

            //apotti_type
            $apotti_type = $request->apotti_type;
            $query->when($apotti_type, function ($query) use ($apotti_type) {
                return $query->where('memo_type', $apotti_type);
            });

            //memo_status
            $memo_status = $request->memo_status;
            $query->when($memo_status, function ($query) use ($memo_status) {
                return $query->where('memo_status', $memo_status);
            });

            //memo_status
            $project_id = $request->project_id;
            $query->when($project_id, function ($query) use ($project_id) {
                return $query->whereIn('project_id', $project_id);
            });


            if ($request->doner_id && !$project_id) {
                $query->where('project_id', '-1');
            }


            //jorito_ortho_poriman
            $total_jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $query->when($total_jorito_ortho_poriman, function ($query) use ($total_jorito_ortho_poriman) {
                $total_jorito_ortho_poriman = bnToen(str_replace(",", "", $total_jorito_ortho_poriman));
                return $query->where('jorito_ortho_poriman', $total_jorito_ortho_poriman);
            });

            //file_token_no
            $file_token_no = $request->file_token_no;
            $query->when($file_token_no, function ($query) use ($file_token_no) {
                return $query->where('file_token_no', $file_token_no);
            });

            $data['total_jorito_ortho_poriman'] = number_format($query->sum('jorito_ortho_poriman'), 4, '.', '');
            $data['apotti_list'] = $query->with(['fiscal_year'])->paginate($request->per_page ?: config('bee_config.per_page_pagination'));

            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function view(Request $request)
    {
        try {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $apotti = Apotti::with(['fiscal_year:id,start,end'])
                ->with(['apotti_items.apotti_attachment'])
                ->where('id', $request->apotti_id)
                ->first()
                ->toArray();

            $memo_ids = ApottiItem::where('apotti_id', $apotti['id'])->pluck('memo_id');
            $attachments = AcMemoAttachment::whereIn('ac_memo_id', $memo_ids)->get()->toArray();

            $data['apotti'] = $apotti;
            $data['attachments'] = $attachments;
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function edit(Request $request): array
    {
        try {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $data['apotti_item'] = ApottiItem::where('id', $request->apotti_item_id)->first()->toArray();

            $data['apotti'] = Apotti::where('id', $data['apotti_item']['apotti_id'])->first()->toArray();

            $memo_id = $data['apotti_item']['memo_id'];

            $data['main_attachments'] = AcMemoAttachment::where('ac_memo_id', $memo_id)
                ->where('file_type', 'main')->get()->toArray();

            $data['promanok_attachments'] = AcMemoAttachment::where('ac_memo_id', $memo_id)
                ->where('file_type', 'promanok')->get()->toArray();

            $data['porisishto_attachments'] = AcMemoAttachment::where('ac_memo_id', $memo_id)
                ->where('file_type', 'porisishto')->get()->toArray();

            $data['other_attachments'] = AcMemoAttachment::where('ac_memo_id', $memo_id)
                ->where('file_type', 'other')->get()->toArray();

            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeEditedApotti(Request $request)
    {
        DB::beginTransaction();
        try {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $apotti_item = ApottiItem::where('id', $request->apotti_item_id)->first();

            $memo_id = $apotti_item->memo_id;

            $apotti_item->memo_type = $request->memo_type;
            $apotti_item->audit_type = $request->audit_type;
            $apotti_item->fiscal_year_id = $request->fiscal_year_id;
            $apotti_item->audit_year_start = $request->audit_year_start;
            $apotti_item->audit_year_end = $request->audit_year_end;

            if ($request->cost_center_id) {
                $apotti_item->cost_center_id = $request->cost_center_id;
                $apotti_item->cost_center_name_en = $request->cost_center_name_en;
                $apotti_item->cost_center_name_bn = $request->cost_center_name_bn;
            }

            if ($request->parent_office_id) {
                $apotti_item->parent_office_id = $request->parent_office_id;
                $apotti_item->parent_office_name_en = $request->parent_office_name_en;
                $apotti_item->parent_office_name_bn = $request->parent_office_name_bn;
            }

            if ($request->ministry_id) {
                $apotti_item->ministry_id = $request->ministry_id;
                $apotti_item->ministry_name_en = $request->ministry_name_en;
                $apotti_item->ministry_name_bn = $request->ministry_name_bn;
            }
            if ($request->project_id) {
                $apotti_item->project_id = $request->project_id;
                $apotti_item->project_name_en = $request->project_name_en;
                $apotti_item->project_name_bn = $request->project_name_bn;
            }


            $apotti_item->save();


            $apotti = Apotti::where('id', $apotti_item->apotti_id)->first();
            $apotti->fiscal_year_id = $request->fiscal_year_id;
            $apotti->apotti_type = $request->apotti_type;

            if ($request->parent_office_id) {
                $apotti->parent_office_id = $request->parent_office_id;
                $apotti->parent_office_name_en = $request->parent_office_name_en;
                $apotti->parent_office_name_bn = $request->parent_office_name_bn;
            }

            if ($request->ministry_id) {
                $apotti->ministry_id = $request->ministry_id;
                $apotti->ministry_name_en = $request->ministry_name_en;
                $apotti->ministry_name_bn = $request->ministry_name_bn;
            }

            if ($request->project_id) {
                $apotti->project_id = $request->project_id;
                $apotti->project_name_en = $request->project_name_en;
                $apotti->project_name_bn = $request->project_name_bn;
            }

            $apotti->save();


            $memo = AcMemo::where('id', $memo_id)->first();
            $memo->audit_type = $request->audit_type;
            $memo->fiscal_year_id = $request->fiscal_year_id;
            $memo->fiscal_year = $request->fiscal_year;
            $memo->audit_year_start = $request->audit_year_start;
            $memo->audit_year_end = $request->audit_year_end;

            if ($request->cost_center_id) {
                $memo->cost_center_id = $request->cost_center_id;
                $memo->cost_center_name_en = $request->cost_center_name_en;
                $memo->cost_center_name_bn = $request->cost_center_name_bn;
            }

            if ($request->parent_office_id) {
                $memo->parent_office_id = $request->parent_office_id;
                $memo->parent_office_name_en = $request->parent_office_name_en;
                $memo->parent_office_name_bn = $request->parent_office_name_bn;
            }

            if ($request->ministry_id) {
                $memo->ministry_id = $request->ministry_id;
                $memo->ministry_name_en = $request->ministry_name_en;
                $memo->ministry_name_bn = $request->ministry_name_bn;
            }
            if ($request->project_id) {
                $memo->project_id = $request->project_id;
                $memo->project_name_en = $request->project_name_en;
                $memo->project_name_bn = $request->project_name_bn;
            }

            $memo->save();

            $rpu_edit = $this->initRPUHttp()->post(config("cag_rpu_api.store-edited-apotti"), $request->all())->json();

            if (!isSuccess($rpu_edit)) {
                throw new \Exception(json_encode($rpu_edit));
            }

            DB::commit();
            return ['status' => 'success', 'data' => 'Apotti updated successfully'];
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getMinistryWiseProject(Request $request)
    {
        try {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $query = ApottiItem::query();
            $query = $query->select('id', 'memo_irregularity_type', 'memo_irregularity_sub_type', 'memo_status', 'project_id', 'project_name_en', 'project_name_bn');
            if ($request->ministry_id) {
                $query = $query->where('ministry_id', $request->ministry_id);
            }

            if ($request->type == 'only_id') {
                $project_list = $query->distinct()->pluck('project_id');

            } else if ($request->type == 'by_project_ids') {
                $project_list = $query->whereIn('project_id', $request->project_ids)
                    ->get()
                    ->unique('project_id');
            } else {
                $project_list = $query->distinct('project_id')
                    ->get();
            }

            return ['status' => 'success', 'data' => $project_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
