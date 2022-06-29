<?php

namespace App\Services;

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

            $query = Apotti::query();

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


            //apotti_type
            $apotti_type = $request->apotti_type;
            $query->when($apotti_type, function ($query) use ($apotti_type) {
                return $query->where('apotti_type', $apotti_type);
            });

            //jorito_ortho_poriman
            $total_jorito_ortho_poriman = $request->total_jorito_ortho_poriman;
            $query->when($total_jorito_ortho_poriman, function ($query) use ($total_jorito_ortho_poriman) {
                $total_jorito_ortho_poriman = bnToen(str_replace(",","",$total_jorito_ortho_poriman));
                return $query->where('total_jorito_ortho_poriman', $total_jorito_ortho_poriman);
            });

            //file_token_no
            $file_token_no = $request->file_token_no;
            $query->when($file_token_no, function ($query) use ($file_token_no) {
                return $query->where('file_token_no', $file_token_no);
            });

            $apotti_list = $query->with(['fiscal_year'])->paginate($request->per_page ?: config('bee_config.per_page_pagination'));
            return ['status' => 'success', 'data' => $apotti_list];
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
                ->where('id',$request->apotti_id)
                ->first()
                ->toArray();

            $memo_ids = ApottiItem::where('apotti_id',$apotti['id'])->pluck('memo_id');
            $attachments = AcMemoAttachment::whereIn('ac_memo_id',$memo_ids)->get()->toArray();

            $data['apotti'] = $apotti;
            $data['attachments'] = $attachments;
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
