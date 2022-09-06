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
                return $query->where('project_id', $project_id);
            });

            //jorito_ortho_poriman
            $total_jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $query->when($total_jorito_ortho_poriman, function ($query) use ($total_jorito_ortho_poriman) {
                $total_jorito_ortho_poriman = bnToen(str_replace(",","",$total_jorito_ortho_poriman));
                return $query->where('jorito_ortho_poriman', $total_jorito_ortho_poriman);
            });

            //file_token_no
            $file_token_no = $request->file_token_no;
            $query->when($file_token_no, function ($query) use ($file_token_no) {
                return $query->where('file_token_no', $file_token_no);
            });

            $data['total_jorito_ortho_poriman'] = number_format($query->sum('jorito_ortho_poriman'),4, '.', '');
            $data['apotti_list']  = $query->with(['fiscal_year'])->paginate($request->per_page ?: config('bee_config.per_page_pagination'));

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
