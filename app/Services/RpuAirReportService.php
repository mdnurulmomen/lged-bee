<?php

namespace App\Services;
use App\Models\AcMemoAttachment;
use App\Models\AnnualPlanEntitie;
use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\ApottiRAirMap;
use App\Models\BroadSheetReply;
use App\Models\BroadSheetReplyItem;
use App\Models\RAir;
use App\Models\XDefaultSetting;
use App\Models\XFiscalYear;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RpuAirReportService
{
    use GenericData, ApiHeart;

    public function airSendToRpu(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $air_info = RAir::where('id',$request->air_id)->first();
            $fiscal_year = XFiscalYear::find($air_info->fiscal_year_id);

            //get apotti map data
            $apotti_map_list = ApottiRAirMap::where('rairs_id',$request->air_id)
                ->where('is_delete',0)->pluck('apotti_id');

            //get apotti list
            $apotti_list = Apotti::with(['apotti_items'])
                ->whereIn('id',$apotti_map_list)
                ->where(function($query){
                    $query->where('apotti_type','sfi')
                        ->orWhere('apotti_type','non-sfi');
                })->get()->toArray();

            //return ['status' => 'error', 'data' => $apotti_list];

            //get entity list
            $entity_list = AnnualPlanEntitie::where('annual_plan_id',$air_info->annual_plan_id)->get();

            $air_list = [];
            foreach ($entity_list as $entity){
                $data['report_number'] = $air_info->report_number;
                $data['report_name'] = $air_info->report_name;
                $data['air_id'] = $air_info->id;
                $data['fiscal_year_id'] = $air_info->fiscal_year_id;
                $data['cost_center_id'] = $entity->entity_id;
                $data['fiscal_year'] = $fiscal_year->start.'-'.$fiscal_year->end;
                $data['annual_plan_id'] = $air_info->annual_plan_id;
                $data['audit_plan_id'] = $air_info->audit_plan_id;
                $data['activity_id'] = $air_info->activity_id;
                $data['air_description'] = gzuncompress(getDecryptedData($air_info->air_description));
                $data['directorate_id'] = $cdesk->office_id;
                $data['directorate_en'] = $cdesk->office_name_en;
                $data['directorate_bn'] = $cdesk->office_name_bn;
                $data['sender_id'] = $cdesk->officer_id;
                $data['sender_en'] = $cdesk->officer_en;
                $data['sender_bn'] = $cdesk->officer_bn;
                $data['send_date'] = date('Y-m-d');

                $air_list[] = $data;

            }

            //send rp
            if (!empty($air_list)){
                $send_air_data['air_list'] = $air_list;
                $send_air_data['apotti_list'] = $apotti_list;
                $send_air_data['air_id'] = $request->air_id;
                $send_air_data['directorate_id'] = $cdesk->office_id;
                $send_air_data['directorate_en'] = $cdesk->office_name_en;
                $send_air_data['directorate_bn'] = $cdesk->office_name_bn;
                $send_air_data['fiscal_year'] = $fiscal_year->start.'-'.$fiscal_year->end;
                $send_air_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.send_air_to_rpu'), $send_air_data)->json();

                if ($send_air_to_rpu['status'] == 'success') {
                    $air_info->is_sent = 1;
                    $air_info->save();

                    //tagid potro send
                    /*$tagidPotroSendingDays = XDefaultSetting::select('setting_value')
                        ->where('setting_key','tagid_potro_sending_days')
                        ->where('is_active',1)
                        ->first()
                        ->toArray();

                    $tagid_potro_task_data = [
                        'task_title_en' => $air_info->report_name.' এ তাগিদ পত্র প্রেরণ করুন',
                        'task_title_bn' => $air_info->report_name.' এ তাগিদ পত্র প্রেরণ করুন',
                        'description' => '',
                        'meta_data' => base64_encode(json_encode(['r_air_id' => $air_info->id, 'return_url' => ''])),
                        'task_start_end_date_time' => Carbon::now()->addDays($tagidPotroSendingDays['setting_value'])->format('d/m/Y H:i A') . ' - ' . Carbon::now()->addDays($tagidPotroSendingDays['setting_value'])->format('d/m/Y H:i A'),
                        'notifications' => json_encode([[
                            "medium" => "email",
                            "interval" => "1",
                            "unit" => "days",
                        ]]),
                    ];
                    (new AmmsPonjikaServices())->createTask($tagid_potro_task_data, $cdesk);*/

                    //do letter sent
                    /*$doLetterSendingDays = XDefaultSetting::select('setting_value')
                        ->where('setting_key','do_letter_sending_days')
                        ->where('is_active',1)
                        ->first()
                        ->toArray();

                    $do_letter_task_data = [
                        'task_title_en' => $air_info->report_name.' এ ডিও লেটার প্রেরণ করুন',
                        'task_title_bn' => $air_info->report_name.' এ ডিও লেটার প্রেরণ করুন',
                        'description' => '',
                        'meta_data' => base64_encode(json_encode(['r_air_id' => $air_info->id, 'return_url' => ''])),
                        'task_start_end_date_time' => Carbon::now()->addDays($doLetterSendingDays['setting_value'])->format('d/m/Y H:i A') . ' - ' . Carbon::now()->addDays($doLetterSendingDays['setting_value'])->format('d/m/Y H:i A'),
                        'notifications' => json_encode([[
                            "medium" => "email",
                            "interval" => "1",
                            "unit" => "days",
                        ]]),
                    ];
                    (new AmmsPonjikaServices())->createTask($do_letter_task_data, $cdesk);*/


                    //status review send
                    $status_review_days = XDefaultSetting::select('setting_value')
                        ->where('setting_key','status_review_days')
                        ->where('is_active',1)
                        ->first();

                    RAir::where('id',$request->air_id)->update([
                        'issue_date'=> Carbon::now()->format('Y-m-d')
                    ]);

                    Apotti::whereIn('id',$apotti_map_list)->update([
                        'is_sent_rp'=> 1,
                        'air_issue_date'=> Carbon::now()->format('Y-m-d'),
                        'status_review_date'=> Carbon::now()->addDays($status_review_days->setting_value)->format('Y-m-d')
                    ]);

                    /*foreach ($apotti_list as $apotti){
                        $status_review_data = [
                            'task_title_en' => 'আপত্তি পর্যালোচনা করুন ('.$apotti->apotti_title.')',
                            'task_title_bn' => 'আপত্তি পর্যালোচনা করুন ('.$apotti->apotti_title.')',
                            'description' => '',
                            'meta_data' => base64_encode(json_encode(['apotti_id' => $apotti->id, 'return_url' => ''])),
                            'task_start_end_date_time' => Carbon::now()->addDays($status_review_days->setting_value)->format('d/m/Y H:i A') . ' - ' . Carbon::now()->addDays($status_review_days->setting_value)->format('d/m/Y H:i A'),
                            'notifications' => json_encode([[
                                "medium" => "email",
                                "interval" => "1",
                                "unit" => "days",
                            ]]),
                        ];
                        (new AmmsPonjikaServices())->createTask($status_review_data, $cdesk);
                    }*/

                    return ['status' => 'success', 'data' => 'Air Send Successfully'];
                }
                return ['status' => 'error', 'data' => $send_air_to_rpu];
            }else{
                return ['status' => 'error', 'data' => 'Entity not found'];
            }
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function receivedAirByRpu(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $air_info = RAir::find($request->air_id);
            $air_info->is_received = 1;
            $air_info->save();
            return ['status' => 'success', 'data' => 'Air Received Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function apottiItemResponseByRpu(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->directorate_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            //for broadsheet reply
//            return ['status' => 'success', 'data' => $request->all()];

            if ($request->hasfile('broad_sheet_hard_copy')) {
                $file = $request->broad_sheet_hard_copy;
                $fileExtension = $file->extension();
                $file_name = 'braod_sheet_heard_copy_' . uniqid() . '.' . $fileExtension;
                $folder_name = $request->directorate_id;
                Storage::disk('public')->put('broad_sheet_hard_copy/' . $folder_name . '/' . $file_name, File::get($file));

                $file_path = 'storage/broad_sheet_hard_copy/' . $folder_name . '/' . $file_name;
            }

            $broadSheetReply =  new BroadSheetReply();
            $broadSheetReply->id = $request->broadsheet_reply_id;
            $broadSheetReply->memorandum_no = $request->memorandum_no;
            $broadSheetReply->memorandum_date = $request->memorandum_date;
            $broadSheetReply->broad_sheet_type = $request->broad_sheet_type;
            $broadSheetReply->sender_office_id = $request->sender_office_id;
            $broadSheetReply->sender_office_name_bn = $request->sender_office_name_bn;
            $broadSheetReply->sender_office_name_en = $request->sender_office_name_en;
            $broadSheetReply->sender_name_bn = $request->sender_name_bn;
            $broadSheetReply->sender_name_en = $request->sender_name_en;
            $broadSheetReply->sender_designation_bn = $request->sender_designation_bn;
            $broadSheetReply->sender_designation_en = $request->sender_designation_en;
            $broadSheetReply->sender_type = $request->sender_type;
            $broadSheetReply->receiver_details = $request->receiver_details;
            $broadSheetReply->subject = $request->subject;
            $broadSheetReply->details = $request->details;
            $broadSheetReply->cc_list = $request->cc_list;
            $broadSheetReply->ministry_id = $request->ministry_id;
            $broadSheetReply->ministry_name_en = $request->ministry_name_en;
            $broadSheetReply->ministry_name_bn = $request->ministry_name_bn;
            $broadSheetReply->broad_sheet_hard_copy = isset($file_path) ? $file_path : null;
            $broadSheetReply->save();

            //broadsheet reply item
            $finalAttachments = [];
            //return ['status' => 'success', 'data' => $request->apottiItems];
            foreach (json_decode($request->apottiItems,true) as $apottiItem){
                $broadSheetReplyItem =  new BroadSheetReplyItem();
                $broadSheetReplyItem->broad_sheet_reply_id = $request->broadsheet_reply_id;
                $broadSheetReplyItem->apotti_id = $apottiItem['apotti_id'];
                $broadSheetReplyItem->apotti_item_id = $apottiItem['apotti_item_id'];
                $broadSheetReplyItem->memo_id = $apottiItem['memo_id'];
                $broadSheetReplyItem->jorito_ortho_poriman = $apottiItem['jorito_ortho_poriman'];
                $broadSheetReplyItem->unit_response = $apottiItem['unit_response'];
                $broadSheetReplyItem->entity_response = $apottiItem['entity_response'];
                $broadSheetReplyItem->ministry_response = $apottiItem['ministry_response'];
                $broadSheetReplyItem->save();

                //apotti item
                ApottiItem::where('id',$apottiItem['apotti_item_id'])
                    ->update([
                        'unit_response' => $apottiItem['unit_response'],
                        'entity_response' => $apottiItem['entity_response'],
                        'ministry_response' => $apottiItem['ministry_response'],
                    ]);

                //return ['status' => 'success', 'data' => $apottiItem['apotti_attachements']];

                if (!empty($apottiItem['apotti_attachements'])){
                    foreach ($apottiItem['apotti_attachements'] as $attachment){
                        array_push($finalAttachments, array(
                                'ac_memo_id' => $attachment['ac_memo_id'],
                                'file_type' => $attachment['file_type'],
                                'file_user_define_name' => $attachment['file_user_define_name'],
                                'file_custom_name' => $attachment['file_custom_name'],
                                'file_path' => $attachment['file_path'],
                                'file_size' => $attachment['file_size'],
                                'file_extension' => $attachment['file_extension'],
                                'sequence' => $attachment['sequence'],
                                'created_by' => 1,
                                'modified_by' => 1,
                            )
                        );
                    }
                }
            }

            if (!empty($finalAttachments)) {
                AcMemoAttachment::insert($finalAttachments);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'সফলভাবে জবাব প্রেরণ করা হয়েছে'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
