<?php

namespace App\Services;

use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\BroadSheetMovement;
use App\Models\BroadSheetReply;
use App\Models\BroadSheetReplyItem;
use App\Models\BroadsheetReplyToResponsibleParty;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use DB;

class BroadsheetReplyService
{
    use GenericData, ApiHeart;

    public function getBroadSheetList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);

        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $ministry_id = $request->ministry_id;
            $entity_id = $request->entity_id;

            $query = BroadSheetReply::query();

            if($ministry_id && $entity_id){
                $query->where('ministry_id',$ministry_id);
                $query->where('sender_office_id',$entity_id);
                $query->where('sender_type','entity');
            } elseif($ministry_id){
                $query->where('ministry_id',$ministry_id);
                $query->where('sender_type','!=','entity');
            }

            $broad_sheet_list =  $query->with('latest_broad_sheet_movement')
                                 ->with('broad_sheet_reply')
                                 ->withCount('broad_sheet_items')
                                    ->withCount([
                                        'broad_sheet_items as broad_sheet_item_decision' => function ($query) {
                                            $query->where('approval_status','approved');
                                        },
                                    ])
                                ->orderBy('id','DESC')
                                ->paginate(config('bee_config.per_page_pagination'));

            return ['status' => 'success', 'data' => $broad_sheet_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getBroadSheetInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $broad_sheet_info = BroadSheetReply::find($request->broad_sheet_id);
            return ['status' => 'success', 'data' => $broad_sheet_info];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getBroadSheetItems(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $broad_sheet_list = BroadSheetReplyItem::with('apotti.fiscal_year')->with('apotti.apotti_attachment')->where('broad_sheet_reply_id',$request->broad_sheet_id)->get();

            return ['status' => 'success', 'data' => $broad_sheet_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getBroadSheetItemInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $broad_sheet_item_info = BroadSheetReplyItem::where('broad_sheet_reply_id',$request->broad_sheet_id)
                ->where('memo_id',$request->memo_id)
                ->first();

            return ['status' => 'success', 'data' => $broad_sheet_item_info];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function updateBroadSheetItem(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $approval_status = $request->approval_status;

            $broad_sheet_list = BroadSheetReplyItem::where('broad_sheet_reply_id',$request->broad_sheet_id)
                                                    ->where('memo_id',$request->memo_id)->first();

            $broad_sheet_list->status = $request->apotti_status ?: $broad_sheet_list->status;
            $broad_sheet_list->jorito_ortho_poriman = $request->jorito_ortho ?: $broad_sheet_list->jorito_ortho_poriman;
            $broad_sheet_list->onishponno_jorito_ortho_poriman = $request->jorito_ortho - ($request->collected_amount + $request->adjusted_amount);
            $broad_sheet_list->collected_amount = $request->collected_amount ?: $broad_sheet_list->collected_amount;
            $broad_sheet_list->adjusted_amount = $request->adjusted_amount ?: $broad_sheet_list->adjusted_amount;
            $broad_sheet_list->comment = $request->comment ?: $broad_sheet_list->comment;
            $broad_sheet_list->cag_comment = $request->cag_comment ?: $broad_sheet_list->cag_comment;
            $broad_sheet_list->status_reason = $request->status_reason ?: $broad_sheet_list->status_reason;

            if($approval_status){
                $broad_sheet_list->approval_status = $request->approval_status;
                $broad_sheet_list->approved_by = $cdesk->officer_id;
                $broad_sheet_list->approver_bn  = $cdesk->officer_bn;
                $broad_sheet_list->approver_en  = $cdesk->officer_en;
                $broad_sheet_list->approver_designation_id  = $cdesk->designation_id;
                $broad_sheet_list->approver_designation_bn  = $cdesk->designation_bn;
                $broad_sheet_list->approver_designation_en  = $cdesk->designation_en;
            }

            $broad_sheet_list->save();

            return ['status' => 'success', 'data' => 'সিদ্ধান্ত দেয়া হয়েছে'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function approveBroadSheetItem(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $broad_sheet_list = BroadSheetReplyItem::where('broad_sheet_reply_id', $request->broad_sheet_id)
                ->where('apotti_item_id', $request->apotti_item_id)->first();

            $broad_sheet_list->approval_status = $request->approval_status;
            $broad_sheet_list->approved_by = $cdesk->officer_id;
            $broad_sheet_list->approver_bn = $cdesk->officer_bn;
            $broad_sheet_list->approver_en = $cdesk->officer_en;
            $broad_sheet_list->approver_designation_id = $cdesk->designation_id;
            $broad_sheet_list->approver_designation_bn = $cdesk->designation_bn;
            $broad_sheet_list->approver_designation_en = $cdesk->designation_en;

            $broad_sheet_list->save();

            $apotti_item = ApottiItem::find($request->apotti_item_id);
            $apotti_item->memo_status = $broad_sheet_list->status;
            $apotti_item->onishponno_jorito_ortho_poriman = $broad_sheet_list->onishponno_jorito_ortho_poriman;
            $apotti_item->adjustment_ortho_poriman = $broad_sheet_list->adjusted_amount;
            $apotti_item->save();

            $data['apotti_item_id'] = $request->apotti_item_id;
            $data['memo_status'] = $broad_sheet_list->status;
            $data['onishponno_jorito_ortho_poriman'] = $broad_sheet_list->onishponno_jorito_ortho_poriman;
            $data['adjustment_ortho_poriman'] = $broad_sheet_list->adjusted_amount;
            $data['collected_amount'] = $broad_sheet_list->collected_amount;
            $data['directorate_id'] = $cdesk->office_id;

            return ['status' => 'success', 'data' => 'অনুমোদন দেয়া হয়েছে'];

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
            $apottiIteminfo = ApottiItem::with(['fiscal_year'])->find($request->apotti_item_id);
            return ['status' => 'success', 'data' => $apottiIteminfo];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function broadSheetMovement(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ?: $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            //broad sheet movement data
            $broadSheetMovementData = [
                'broad_sheet_id' => $request->broad_sheet_id,
                'receiver_officer_id' => $request->receiver_officer_id,
                'receiver_office_id' => $request->receiver_office_id,
                'receiver_unit_id' => $request->receiver_unit_id,
                'receiver_unit_name_en' => $request->receiver_unit_name_en,
                'receiver_unit_name_bn' => $request->receiver_unit_name_bn,
                'receiver_employee_id' => $request->receiver_employee_id,
                'receiver_employee_name_en' => $request->receiver_employee_name_en,
                'receiver_employee_name_bn' => $request->receiver_employee_name_bn,
                'receiver_employee_designation_id' => $request->receiver_employee_designation_id,
                'receiver_employee_designation_en' => $request->receiver_employee_designation_en,
                'receiver_employee_designation_bn' => $request->receiver_employee_designation_bn,
                'receiver_officer_phone' => $request->receiver_officer_phone,
                'receiver_officer_email' => $request->receiver_officer_email,
                'sender_officer_id' => $cdesk->officer_id,
                'sender_office_id' => $cdesk->office_id,
                'sender_unit_id' => $cdesk->office_unit_id,
                'sender_unit_name_en' => $cdesk->office_unit_en,
                'sender_unit_name_bn' => $cdesk->office_unit_bn,
                'sender_employee_id' => $cdesk->officer_id,
                'sender_employee_name_en' => $cdesk->officer_en,
                'sender_employee_name_bn' => $cdesk->officer_bn,
                'sender_employee_designation_id' => $cdesk->designation_id,
                'sender_employee_designation_en' => $cdesk->designation_en,
                'sender_employee_designation_bn' => $cdesk->designation_bn,
                'sender_officer_phone' => $cdesk->phone,
                'sender_officer_email' => $cdesk->email,
                'comments' => $request->comments
            ];

            BroadSheetMovement::create($broadSheetMovementData);

            return ['status' => 'success', 'data' => ['সফলভাবে প্রেরণ করা হয়েছে']];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function broadSheetLastMovement(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $lastMovementInfo = BroadSheetMovement::where('broad_sheet_id', $request->broad_sheet_id)
                ->latest()
                ->first()
                ->toArray();

            return ['status' => 'success', 'data' => $lastMovementInfo];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeBroadSheetReply(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;

        DB::beginTransaction();
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            BroadsheetReplyToResponsibleParty::updateOrCreate(
                ['broad_sheet_reply_id' => $request->broad_sheet_id],
                [
                    'ref_memorandum_no' => $request->ref_memorandum_no,
                    'memorandum_no' => $request->memorandum_no,
                    'memorandum_date' => $request->memorandum_date,
                    'rpu_office_head_details' => $request->rpu_office_head_details,
                    'subject' => $request->subject,
                    'description' => $request->description,
                    'braod_sheet_cc' => $request->braod_sheet_cc,
                    'is_sent_rpu' => 0,
                    'sender_id' => $cdesk->officer_id,
                    'sender_name_bn' => $cdesk->officer_bn,
                    'sender_name_en' => $cdesk->officer_en,
                    'sender_office_address' => $cdesk->address,
                    'sender_designation_id' => $cdesk->designation_id,
                    'sender_designation_bn' => $cdesk->designation_bn,
                    'sender_designation_en' => $cdesk->designation_en,
                    'sender_unit_id' => $cdesk->office_unit_id,
                    'sender_unit_bn' => $cdesk->office_unit_bn,
                    'sender_unit_en' => $cdesk->office_unit_en,
                ]
            );
            DB::commit();

            return ['status' => 'success', 'data' => 'সফলভাবে সংরক্ষণ করা হয়েছে'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function sendBroadSheetReplyToRpu(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;

        DB::beginTransaction();
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $sent_to_rpu = BroadsheetReplyToResponsibleParty::where('broad_sheet_reply_id', $request->broad_sheet_id)->first();
            $sent_to_rpu->is_sent_rpu = 1;
            $sent_to_rpu->save();
            $broad_sheet_item_list = BroadSheetReplyItem::where('broad_sheet_reply_id', $request->broad_sheet_id)->get();

            $apotti_item_data = [];
            foreach ($broad_sheet_item_list as $item){
                $data['broad_sheet_reply_id'] = $item->broad_sheet_reply_id;
                $data['apotti_id'] = $item->apotti_id;
                $data['apotti_item_id'] = $item->apotti_item_id;
                $data['memo_status'] = $item->status;
                $data['onishponno_jorito_ortho_poriman'] = $item->onishponno_jorito_ortho_poriman;
                $data['adjustment_ortho_poriman'] = $item->adjusted_amount;
                $data['collected_amount'] = $item->collected_amount;
                $data['directorate_id'] = $cdesk->office_id;
                $data['directorate_en'] = $cdesk->office_name_en;
                $data['directorate_bn'] = $cdesk->office_name_bn;
                $data['directorate_response'] = $item->comment;
                $data['status_reason'] = $item->status_reason;
                $apotti_item_data[] = $data;
            }

            $sent_to_rpu->directorate_id = $cdesk->office_id;
            $rpu_response_data['item_info'] =  $apotti_item_data;
            $rpu_response_data['reply_info'] =  $sent_to_rpu;

            $send_decision_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.broad_sheet_reply_from_directorate'), $rpu_response_data)->json();

            if ($send_decision_to_rpu['status'] == 'success') {
                DB::commit();
                return ['status' => 'success', 'data' => 'সফলভাবে রেসপন্সিবল পার্টিতে প্রেরণ করা হয়েছে'];
            }else{
                throw new \Exception(json_encode($send_decision_to_rpu));
            }

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
    public function getSentBroadSheetInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $broad_sheet_sent_info = BroadsheetReplyToResponsibleParty::where('broad_sheet_reply_id',$request->broad_sheet_id)->first();

            return ['status' => 'success', 'data' => $broad_sheet_sent_info];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getAllBroadSheetMinistry(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $ministry_list = BroadSheetReply::select('ministry_id', 'ministry_name_en', 'ministry_name_bn')
                ->distinct()
                ->get();

            return ['status' => 'success', 'data' => $ministry_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getAllBroadSheetMinistryWiseEntity(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $entity_list = BroadSheetReply::select('sender_office_id', 'sender_office_name_bn', 'sender_office_name_en')
                ->where('ministry_id',$request->ministry_id)
                ->where('sender_type','entity')
                ->get();

            return ['status' => 'success', 'data' => $entity_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

}
