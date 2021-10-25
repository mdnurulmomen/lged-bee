<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\AcMemoAttachment;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AcMemoService
{
    use GenericData, ApiHeart;

    public function auditMemoStore(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {

            $plan_member_schedule = AuditVisitCalenderPlanMember::with(['plan_team', 'annual_plan', 'activity', 'office_order'])->where('id', $request->team_member_schedule_id)->first();
            $onucched = AcMemo::where('cost_center_id', $plan_member_schedule->cost_center_id)->where('fiscal_year_id', $plan_member_schedule->fiscal_year_id)->max('onucched_no');
            $onucched = $onucched ? (int)$onucched + 1 : 1;

            $audit_memo = new AcMemo();
            $audit_memo->onucched_no = $onucched;
            $audit_memo->memo_irregularity_type = $request->memo_irregularity_type;
            $audit_memo->memo_irregularity_sub_type = $request->memo_irregularity_sub_type;
            $audit_memo->ministry_id = $plan_member_schedule->plan_team->ministry_id;
            $audit_memo->ministry_name_en = $plan_member_schedule->annual_plan->ministry_name_en;
            $audit_memo->ministry_name_bn = $plan_member_schedule->annual_plan->ministry_name_en;
            $audit_memo->controlling_office_id = $plan_member_schedule->plan_team->controlling_office_id;
            $audit_memo->controlling_office_name_en = $plan_member_schedule->plan_team->controlling_office_name_en;
            $audit_memo->controlling_office_name_bn = $plan_member_schedule->plan_team->controlling_office_name_bn;
            $audit_memo->parent_office_id = $plan_member_schedule->plan_team->entity_id;
            $audit_memo->parent_office_name_en = $plan_member_schedule->plan_team->entity_name_en;
            $audit_memo->parent_office_name_bn = $plan_member_schedule->plan_team->entity_name_bn;
            $audit_memo->cost_center_id = $plan_member_schedule->cost_center_id;
            $audit_memo->cost_center_name_en = $plan_member_schedule->cost_center_name_bn;
            $audit_memo->cost_center_name_bn = $plan_member_schedule->cost_center_name_bn;
            $audit_memo->fiscal_year_id = $plan_member_schedule->fiscal_year_id;
            $audit_memo->ap_office_order_id = $plan_member_schedule->office_order->id;
            $audit_memo->audit_plan_id = $plan_member_schedule->audit_plan_id;
            $audit_memo->audit_year_start = $request->audit_year_start;
            $audit_memo->audit_year_end = $request->audit_year_end;
            $audit_memo->ac_query_potro_no = 1; //todo
            $audit_memo->audit_type = $plan_member_schedule->activity->activity_type;
            $audit_memo->team_id = $plan_member_schedule->team_id;
            $audit_memo->memo_title_bn = $request->memo_title_bn;
            $audit_memo->memo_description_bn = $request->memo_description_bn;
            $audit_memo->memo_type = $request->memo_type;
            $audit_memo->memo_status = $request->memo_status;
            $audit_memo->jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $audit_memo->onishponno_jorito_ortho_poriman = $request->onishponno_jorito_ortho_poriman;
            $audit_memo->response_of_rpu = $request->response_of_rpu;
            $audit_memo->audit_conclusion = $request->audit_conclusion;
            $audit_memo->audit_recommendation = $request->audit_recommendation;
            $audit_memo->created_by = $cdesk->officer_id;
            $audit_memo->approve_status = 'draft';
            $audit_memo->status = 'draft';
            $audit_memo->comment = '';
            $audit_memo->save();

            //for attachments
            $finalAttachments = [];
            if ($request->hasfile('porisishto')) {
                $attachment = $request->porisishto;
                $fileName = uniqid() . '.' . $attachment->extension();

                Storage::disk('public')->put('memo/' . $fileName, File::get($attachment));

                array_push($finalAttachments, array(
                        'ac_memo_id' => $audit_memo->id,
                        'attachment_type' => 'porisishto',
                        'user_define_name' => $fileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => url('storage/memo/' . $fileName),
                        'sequence' => 1,
                        'created_by' => $cdesk->officer_id,
                        'modified_by' => $cdesk->officer_id,
                        'deleted_by' => $cdesk->officer_id,
                    )
                );
            }

            if ($request->hasfile('pramanok')) {
                $attachment = $request->pramanok;
                $fileName = uniqid() . '.' . $attachment->extension();

                Storage::disk('public')->put('memo/' . $fileName, File::get($attachment));

                array_push($finalAttachments, array(
                        'ac_memo_id' => $audit_memo->id,
                        'attachment_type' => 'pramanok',
                        'user_define_name' => $fileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => url('storage/memo/' . $fileName),
                        'sequence' => 1,
                        'created_by' => $cdesk->officer_id,
                        'modified_by' => $cdesk->officer_id,
                        'deleted_by' => $cdesk->officer_id,
                    )
                );
            }
            AcMemoAttachment::insert($finalAttachments);

            return ['status' => 'success', 'data' => 'Memo Saved Successfully'];

//            if ($send_audit_query_to_rpu['status'] == 'success') {
//                \DB::commit();
//                return ['status' => 'success', 'data' => 'Send Successfully'];
//            } else {
//                throw new \Exception(json_encode($send_audit_query_to_rpu));
//            }
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $memo_list = AcMemo::where('audit_plan_id', $request->audit_plan_id)
                ->where('cost_center_id', $request->cost_center_id)
                ->paginate(config('bee_config.per_page_pagination'));
            return ['status' => 'success', 'data' => $memo_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoEdit(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $memo_list = AcMemo::with(['ac_memo_attachments'])
                ->where('id', $request->memo_id)
                ->first();
            return ['status' => 'success', 'data' => $memo_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoUpdate(Request $request): array
    {
        //return ['status' => 'error', 'data' => $request->all()];

        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $audit_memo = AcMemo::find($request->memo_id);
            $audit_memo->onucched_no = '1';
            $audit_memo->memo_irregularity_type = $request->memo_irregularity_type;
            $audit_memo->memo_irregularity_sub_type = $request->memo_irregularity_sub_type;
            $audit_memo->audit_year_start = $request->audit_year_start;
            $audit_memo->audit_year_end = $request->audit_year_end;
            $audit_memo->memo_title_bn = $request->memo_title_bn;
            $audit_memo->memo_description_bn = $request->memo_description_bn;
            $audit_memo->memo_type = $request->memo_type;
            $audit_memo->memo_status = $request->memo_status;
            $audit_memo->jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $audit_memo->onishponno_jorito_ortho_poriman = $request->onishponno_jorito_ortho_poriman;
            $audit_memo->response_of_rpu = $request->response_of_rpu;
            $audit_memo->audit_conclusion = $request->audit_conclusion;
            $audit_memo->audit_recommendation = $request->audit_recommendation;
            $audit_memo->updated_by = $cdesk->officer_id;
            $audit_memo->save();

            //for attachments
            $finalAttachments = [];
            if ($request->hasfile('porisishto')) {
                $attachment = $request->porisishto;
                $fileName = uniqid() . '.' . $attachment->extension();

                Storage::disk('public')->put('memo/' . $fileName, File::get($attachment));

                array_push($finalAttachments, array(
                        'ac_memo_id' => $request->memo_id,
                        'attachment_type' => 'porisishto',
                        'user_define_name' => $fileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => url('storage/memo/' . $fileName),
                        'sequence' => 1,
                        'created_by' => $cdesk->officer_id,
                        'modified_by' => $cdesk->officer_id,
                        'deleted_by' => $cdesk->officer_id,
                    )
                );
            }

            if ($request->hasfile('pramanok')) {
                $attachment = $request->pramanok;
                $fileName = uniqid() . '.' . $attachment->extension();

                Storage::disk('public')->put('memo/' . $fileName, File::get($attachment));

                array_push($finalAttachments, array(
                        'ac_memo_id' => $request->memo_id,
                        'attachment_type' => 'pramanok',
                        'user_define_name' => $fileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => url('storage/memo/' . $fileName),
                        'sequence' => 1,
                        'created_by' => $cdesk->officer_id,
                        'modified_by' => $cdesk->officer_id,
                        'deleted_by' => $cdesk->officer_id,
                    )
                );
            }
            AcMemoAttachment::insert($finalAttachments);

            return ['status' => 'success', 'data' => 'Memo Saved Successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function sendMemoToRpu(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $memo = AcMemo::with('ac_memo_attachments:id,ac_memo_id,attachment_type,user_define_name,attachment_path,sequence')->whereIn('id', $request->memos)->get();

            $data['memos'] = $memo;
            $data['memo_send_date'] = date('Y-m-d');
            $data['sender_officer_id'] = $cdesk->officer_id;
            $data['sender_officer_name_bn'] = $cdesk->officer_bn;
            $data['sender_officer_name_en'] = $cdesk->officer_en;

            $send_audit_memo_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.send_memo_to_rpu'), $data)->json();

            if ($send_audit_memo_to_rpu['status'] == 'success') {
                AcMemo::whereIn('id', $request->memos)->update(['has_sent_to_rpu'=>1]);
                return ['status' => 'success', 'data' => 'Send Successfully'];
            } else {
                throw new \Exception(json_encode($send_audit_memo_to_rpu));
            }
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function authorityMemoList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
//            $memo_list = AcMemo::where('audit_plan_id', $request->audit_plan_id)
//                ->where('cost_center_id', $request->cost_center_id)
//                ->paginate(config('bee_config.per_page_pagination'));

            $fiscal_year_id = $request->fiscal_year_id;
            $cost_center_id = $request->cost_center_id;
            $team_id = $request->team_id;
            $memo_irregularity_type= $request->memo_irregularity_type;
            $memo_irregularity_sub_type = $request->memo_irregularity_sub_type;
            $memo_type = $request->memo_type;
            $memo_status = $request->memo_status;
            $jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $audit_year_start = $request->audit_year_start;
            $audit_year_end = $request->audit_year_end;

            $query = AcMemo::query();

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($cost_center_id, function ($q, $cost_center_id) {
                return $q->where('cost_center_id', $cost_center_id);
            });

            $query->when($team_id, function ($q, $team_id) {
                return $q->where('team_id', $team_id);
            });

            $query->when($memo_irregularity_type, function ($q, $memo_irregularity_type) {
                return $q->where('memo_irregularity_type', $memo_irregularity_type);
            });

            $query->when($memo_irregularity_sub_type, function ($q, $memo_irregularity_sub_type) {
                return $q->where('memo_irregularity_sub_type', $memo_irregularity_sub_type);
            });

            $query->when($memo_type, function ($q, $memo_type) {
                return $q->where('memo_type', $memo_type);
            });

            $query->when($memo_status, function ($q, $memo_status) {
                return $q->where('memo_status', $memo_status);
            });

            $query->when($jorito_ortho_poriman, function ($q, $jorito_ortho_poriman) {
                return $q->where('jorito_ortho_poriman', $jorito_ortho_poriman);
            });

            $query->when($audit_year_start, function ($q, $audit_year_start) {
                return $q->where('audit_year_start', $audit_year_start);
            });

            $query->when($audit_year_end, function ($q, $audit_year_end) {
                return $q->where('audit_year_end', $audit_year_end);
            });

            $memo_list = $query->paginate(config('bee_config.per_page_pagination'));

            return ['status' => 'success', 'data' => $memo_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
