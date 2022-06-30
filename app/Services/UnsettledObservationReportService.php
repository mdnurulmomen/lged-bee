<?php

namespace App\Services;

use App\Models\AcMemoAttachment;
use App\Models\ApottiItem;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class UnsettledObservationReportService
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
            $query = $query->with(['fiscal_year']);

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

            //cost_center
            $cost_center_id = $request->cost_center_id;
            $query->when($cost_center_id, function ($query) use ($cost_center_id) {
                return $query->where('cost_center_id', $cost_center_id);
            });

            //fiscal_year_id
            $fiscal_year_id = $request->fiscal_year_id;
            $query->when($fiscal_year_id, function ($query) use ($fiscal_year_id) {
                return $query->where('fiscal_year_id', $fiscal_year_id);
            });


            //memo_type
            $memo_type = $request->memo_type;
            $query->when($memo_type, function ($query) use ($memo_type) {
                return $query->where('memo_type', $memo_type);
            });

            //jorito_ortho_poriman
            $jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $query->when($jorito_ortho_poriman, function ($query) use ($jorito_ortho_poriman) {
                $jorito_ortho_poriman = bnToen(str_replace(",","",$jorito_ortho_poriman));
                return $query->where('jorito_ortho_poriman', $jorito_ortho_poriman);
            });

            $data['apotti_list']  = $query->where('memo_status',2)->paginate($request->per_page ?: config('bee_config.per_page_pagination'));
            $data['total_jorito_ortho_poriman'] = $query->sum('jorito_ortho_poriman');

            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function download(Request $request)
    {
        try {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $query = ApottiItem::query();
            $query = $query->with(['fiscal_year']);

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

            //cost_center
            $cost_center_id = $request->cost_center_id;
            $query->when($cost_center_id, function ($query) use ($cost_center_id) {
                return $query->where('cost_center_id', $cost_center_id);
            });

            //fiscal_year_id
            $fiscal_year_id = $request->fiscal_year_id;
            $query->when($fiscal_year_id, function ($query) use ($fiscal_year_id) {
                return $query->where('fiscal_year_id', $fiscal_year_id);
            });


            //memo_type
            $memo_type = $request->memo_type;
            $query->when($memo_type, function ($query) use ($memo_type) {
                return $query->where('memo_type', $memo_type);
            });

            //jorito_ortho_poriman
            $jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $query->when($jorito_ortho_poriman, function ($query) use ($jorito_ortho_poriman) {
                $jorito_ortho_poriman = bnToen(str_replace(",","",$jorito_ortho_poriman));
                return $query->where('jorito_ortho_poriman', $jorito_ortho_poriman);
            });

            $data['apotti_list'] = $query->where('memo_status',2)->get()->toArray();
            $data['total_jorito_ortho_poriman'] = $query->sum('jorito_ortho_poriman');

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
            $apotti = ApottiItem::with(['fiscal_year:id,start,end','apotti_attachment'])
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
