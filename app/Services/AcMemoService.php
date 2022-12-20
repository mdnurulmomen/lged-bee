<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\AcMemoAttachment;
use App\Models\AcMemoLog;
use App\Models\AcMemoPorisishto;
use App\Models\AcMemoRecommendation;
use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\OfficeDomain;
use App\Models\XFiscalYear;
use App\Models\XResponsibleOffice;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Carbon\Carbon;
use DB;
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
            $folder_name = $cdesk->office_id;

            $path = public_path('/memo/' . $folder_name);

            /*if (!Storage::exists($path)) {
                $create_directorate_folder = Storage::makeDirectory($path, 0777, true, true);
                if (!$create_directorate_folder) {
                    throw new \Exception('Error creating memo folder');
                }
            }*/

            // $office_domain_prefix = $office_db_con_response['office_domain']['domain_prefix'];

            // $plan_member_schedule = AuditVisitCalenderPlanMember::with(['plan_team', 'annual_plan', 'activity',
            //     'office_order'])->where('id', $request->team_member_schedule_id)->first();

            // $onucched = AcMemo::where('cost_center_id', $plan_member_schedule->cost_center_id)
            //     ->where('fiscal_year_id', $plan_member_schedule->fiscal_year_id)->count();

            // $fiscal_year_info = XFiscalYear::select('start', 'end')->where('id', $plan_member_schedule->fiscal_year_id)->first();

            $audit_memo = new AcMemo();
            $audit_memo->audit_observation = $request->audit_observation;
            $audit_memo->heading = $request->heading;
            $audit_memo->criteria = $request->criteria;
            $audit_memo->condition = $request->condition;
            $audit_memo->cause = $request->cause;
            $audit_memo->instances = $request->instances;

            $audit_memo->cost_center_id = $request->cost_center_id;
            $audit_memo->cost_center_name_bn = $request->cost_center_name_bn;
            $audit_memo->cost_center_name_en = $request->cost_center_name_en;

            $audit_memo->audit_plan_id = $request->audit_plan_id;
            $audit_memo->audit_year_start = $request->audit_year_start;
            $audit_memo->audit_year_end = $request->audit_year_end;

            $audit_memo->memo_type = 1;
            $audit_memo->memo_status = 1;
            $audit_memo->action_type = $request->action_type;
            $audit_memo->challenges = $request->challenges;
            $audit_memo->date_to_be_implemented = date('Y-m-d H:i:s', strtotime($request->date_to_be_implemented));
            $audit_memo->save();


            // $porisistos = [];
            // foreach ($request->porisisto_details as $key => $porisisto){
            //     if ($porisisto != null){
            //         $porisistos[] = array(
            //             'ac_memo_id' => $audit_memo->id,
            //             'details' => $porisisto,
            //             'sequence' => $key + 1,
            //             'created_by' => $cdesk->officer_id
            //         );
            //     }
            // }
            // if (!empty($porisistos)) {
            //     AcMemoPorisishto::insert($porisistos);
            // }

            //for attachments
            // $finalAttachments = [];

            //for porisishtos
            /*if ($request->hasfile('porisishtos')) {
                foreach ($request->porisishtos as $key => $file) {
                    $userDefineFile = $file->getClientOriginalName();
                    $userDefineFileName = explode('.',$userDefineFile)[0];
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = $office_domain_prefix . '_porisishto_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('memo/' . $folder_name . '/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'ac_memo_id' => $audit_memo->id,
                        'file_type' => 'porisishto',
                        'file_user_define_name' => $userDefineFileName,
                        'file_custom_name' => $fileName,
                        'file_path' => url('storage/memo/' . $folder_name . '/' . $fileName),
                        'file_size' => $fileSize,
                        'file_extension' => $fileExtension,
                        'sequence' => $key + 1,
                        'created_by' => $cdesk->officer_id,
                        'modified_by' => $cdesk->officer_id,
                    );
                }
            }*/

            //for memos
            if ($request->hasfile('memos')) {
                foreach ($request->memos as $key => $file) {
                    $userDefineFile = $file->getClientOriginalName();
                    $userDefineFileName = explode('.',$userDefineFile)[0];
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = '_memo_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('memo/' . $folder_name . '/' . $fileName, File::get($file));

                    $finalAttachments[] = array(
                        'ac_memo_id' => $audit_memo->id,
                        'file_type' => 'memo',
                        'file_user_define_name' => $userDefineFileName,
                        'file_custom_name' => $fileName,
                        'file_path' => '/storage/memo/' . $folder_name . '/',
                        'file_size' => $fileSize,
                        'file_extension' => $fileExtension,
                        'sequence' => $key + 1,
                        'created_by' => $cdesk->officer_id,
                        'modified_by' => $cdesk->officer_id,
                    );
                }
            }

            if (!empty($finalAttachments)) {
                AcMemoAttachment::insert($finalAttachments);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'Memo Saved Successfully'];
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
            $memo_list = AcMemo::with(['ac_memo_attachments'])
            ->where('audit_plan_id',$request->audit_plan_id)
            ->where('cost_center_id',$request->cost_center_id)
            ->get();
            return ['status' => 'success', 'data' => $memo_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoEdit(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        if ($request->has('directorate_id')) {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
        } else {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
        }
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $memo = AcMemo::with(['ac_memo_attachments','ac_memo_porisishtos'])
                ->where('id', $request->memo_id)
                ->first();

            return ['status' => 'success', 'data' => $memo];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function singleAuditMemoInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->directorate_id ? $request->directorate_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $data['memo'] = AcMemo::with(['ac_memo_porisishtos'])->where('id', $request->memo_id)->first();
            $data['porisishto_list'] = AcMemoAttachment::where('ac_memo_id', $request->memo_id)
                ->where('file_type', 'porisishto')->get();
            $data['pramanok_list'] = AcMemoAttachment::where('ac_memo_id', $request->memo_id)
                ->where('file_type', 'pramanok')->get();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoUpdate(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $folder_name = $cdesk->office_id;
            $office_domain_prefix = $office_db_con_response['office_domain']['domain_prefix'];

            $audit_memo = AcMemo::find($request->memo_id);
            $audit_memo->memo_title_bn = $request->memo_title_bn;
            $audit_memo->memo_description_bn = $request->memo_description_bn;
            $audit_memo->irregularity_cause = $request->irregularity_cause;
            $audit_memo->response_of_rpu = $request->response_of_rpu;
            $audit_memo->jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $audit_memo->audit_year_start = $request->audit_year_start;
            $audit_memo->audit_year_end = $request->audit_year_end;
            $audit_memo->memo_irregularity_type = $request->memo_irregularity_type;
            $audit_memo->memo_irregularity_sub_type = $request->memo_irregularity_sub_type;
            $audit_memo->memo_type = $request->memo_type;
            $audit_memo->memo_status = $request->memo_status;
            $audit_memo->finder_officer_id = $request->finder_officer_id;
            $audit_memo->finder_office_id = $request->finder_office_id;
            $audit_memo->finder_details = $request->finder_details;
            $audit_memo->team_leader_name = $request->team_leader_name;
            $audit_memo->team_leader_designation = $request->team_leader_designation;
            $audit_memo->sub_team_leader_name = $request->sub_team_leader_name;
            $audit_memo->sub_team_leader_designation = $request->sub_team_leader_designation;
            $audit_memo->issued_by = $request->issued_by;
            $audit_memo->updated_by = $cdesk->officer_id;

            $changes = array();
            foreach ($audit_memo->getDirty() as $key => $value) {
                $original = $audit_memo->getOriginal($key);
                $changes[$key] = [
                    'old' => $original,
                    'new' => $value,
                ];
            }

            $memo_log = new AcMemoLog();
            $memo_log->memo_content_change = json_encode($changes);
            $memo_log->memo_id = $request->memo_id;
            $memo_log->modified_by_id = $cdesk->officer_id;
            $memo_log->modified_by_name_bn = $cdesk->officer_bn;
            $memo_log->modified_by_name_en = $cdesk->officer_en;
            $memo_log->save();

            $audit_memo->save();

            $porisistos = [];
            if (isset($request->porisisto_details)){
                foreach ($request->porisisto_details as $key=>$porisisto){
                    $porisistos[] = array(
                        'ac_memo_id' => $request->memo_id,
                        'details' => $porisisto,
                        'sequence' => $key + 1,
                        'created_by' => $cdesk->officer_id
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
            /*if ($request->hasfile('porisishtos')) {
                foreach ($request->porisishtos as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = $office_domain_prefix . '_porisishto_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('memo/' . $folder_name . '/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'ac_memo_id' => $audit_memo->id,
                        'file_type' => 'porisishto',
                        'file_user_define_name' => $userDefineFileName,
                        'file_custom_name' => $fileName,
                        'file_path' => url('storage/memo/' . $folder_name . '/' . $fileName),
                        'file_size' => $fileSize,
                        'file_extension' => $fileExtension,
                        'sequence' => $key + 1,
                        'created_by' => $cdesk->officer_id,
                        'modified_by' => $cdesk->officer_id,
                    );
                }
            }*/

            //for pramanoks
            if ($request->hasfile('pramanoks')) {
                foreach ($request->pramanoks as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = $office_domain_prefix . '_pramanok_' . uniqid() . '.' . $file->extension();

                    Storage::disk('public')->put('memo/' . $folder_name . '/' . $fileName, File::get($file));

                    $finalAttachments[] = array(
                        'ac_memo_id' => $audit_memo->id,
                        'file_type' => 'pramanok',
                        'file_user_define_name' => $userDefineFileName,
                        'file_custom_name' => $fileName,
                        'file_path' => url('storage/memo/' . $folder_name . '/' . $fileName),
                        'file_size' => $fileSize,
                        'file_extension' => $fileExtension,
                        'sequence' => $key + 1,
                        'created_by' => $cdesk->officer_id,
                        'modified_by' => $cdesk->officer_id,
                    );
                }
            }
            AcMemoAttachment::insert($finalAttachments);

            $memo_info = AcMemo::with('ac_memo_attachments:id,ac_memo_id,file_type,file_user_define_name,file_path,sequence')->where('id', $request->memo_id)->first();
//            return ['status' => 'success', 'data' => $memo_info];
            if ($memo_info->has_sent_to_rpu) {
                $data['memo_info'] = $memo_info;
                $update_audit_memo_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.update_memo_to_rpu'), $data)->json();
                if ($update_audit_memo_to_rpu['status'] == 'success') {
                    return ['status' => 'success', 'data' => 'Memo Update Successfully'];
                } else {
                    throw new \Exception(json_encode($update_audit_memo_to_rpu));
                }
            } else {
                return ['status' => 'success', 'data' => 'Memo Update Successfully'];
            }

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

        \DB::beginTransaction();
        try {
            //for memo send date
            $memo_send_date = str_replace('/', '-', $request->memo_send_date);

            //for memo info
            $memo = AcMemo::with(['ac_memo_attachments'])->where('id', $request->memo_id)->first();

            //data ready for RP
            $data['memo'] = $memo;
            $data['memo_send_date'] = date('Y-m-d');
            $data['memo_sharok_no'] = $request->memo_sharok_no;
            $data['memo_sharok_date'] = Carbon::parse($memo_send_date)->format('Y-m-d');
            $data['directorate_id'] = $cdesk->office_id;
            $data['directorate_en'] = $cdesk->office_name_en;
            $data['directorate_bn'] = $cdesk->office_name_bn;
            $data['directorate_address'] = $request->directorate_address;
            $data['directorate_website'] = $request->directorate_website;
            $data['rpu_acceptor_designation_name_bn'] = $request->rpu_acceptor_designation_name_bn;
            $data['memo_cc'] = $request->memo_cc;
            $data['issued_by'] = $request->issued_by;
            $data['sender_officer_id'] = $cdesk->officer_id;
            $data['sender_officer_id'] = $cdesk->officer_id;
            $data['sender_officer_name_bn'] = $cdesk->officer_bn;
            $data['sender_officer_name_en'] = $cdesk->officer_en;
            $data['sender_designation_id'] = $cdesk->designation_id;
            $data['sender_designation_en'] = $cdesk->designation_en;
            $data['sender_designation_bn'] = $cdesk->designation_bn;

            $send_audit_memo_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.send_memo_to_rpu'), $data)->json();
            //return ['status' => 'error', 'data' => $send_audit_memo_to_rpu];
            if ($send_audit_memo_to_rpu['status'] == 'success') {
                AcMemo::where('id', $request->memo_id)
                    ->update([
                        'has_sent_to_rpu' => 1,
                        'sender_officer_id' => $cdesk->officer_id,
                        'sender_officer_name_bn' => $cdesk->officer_bn,
                        'sender_officer_name_en' => $cdesk->officer_en,
                        'sender_unit_id' => $cdesk->office_unit_id,
                        'sender_unit_name_bn' => $cdesk->office_unit_bn,
                        'sender_unit_name_en' => $cdesk->office_unit_en,
                        'sender_designation_id' => $cdesk->designation_id,
                        'sender_designation_bn' => $cdesk->designation_bn,
                        'sender_designation_en' => $cdesk->designation_en,
                        'memo_sharok_no' => $request->memo_sharok_no,
                        'memo_send_date' => Carbon::parse($memo_send_date)->format('Y-m-d'),
                        'rpu_acceptor_designation_name_bn' => $request->rpu_acceptor_designation_name_bn,
                        'memo_cc' => $request->memo_cc,
                        'issued_by' => $request->issued_by,
                    ]);
                return ['status' => 'success', 'data' => 'Send Successfully'];
            } else {
                throw new \Exception(json_encode($send_audit_memo_to_rpu));
            }
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function authorityMemoList(Request $request): array
    {

        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $fiscal_year_id = $request->fiscal_year_id;
            $cost_center_id = $request->cost_center_id;
            $entity_id = $request->entity_id;
            $audit_plan_id = $request->audit_plan_id;
            $activity_id = $request->activity_id;
            $team_id = $request->team_id;
            $memo_irregularity_type = $request->memo_irregularity_type;
            $memo_irregularity_sub_type = $request->memo_irregularity_sub_type;
            $memo_type = $request->memo_type;
            $memo_status = $request->memo_status;
            $jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $audit_year_start = $request->audit_year_start;
            $audit_year_end = $request->audit_year_end;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $memo_code = $request->memo_code;
            $finder_officer_id = $request->finder_officer_id;

            $query = AcMemo::query();

            $query->when($memo_code, function ($q, $memo_code) {
                return $q->where('id', $memo_code);
            });

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });


            if(!$memo_code){
                $query->when($activity_id, function ($q, $activity_id) {
                    $q->whereHas('audit_plan', function ($q) use ($activity_id) {
                        return $q->where('activity_id', $activity_id);
                    });
                });
            }

            $query->when($entity_id, function ($q, $entity_id) {
                return $q->where('parent_office_id', $entity_id);
            });

            $query->when($audit_plan_id, function ($q, $audit_plan_id) {
                return $q->where('audit_plan_id', $audit_plan_id);
            });

            $query->when($cost_center_id, function ($q, $cost_center_id) {
                return $q->where('cost_center_id', $cost_center_id);
            });

            $query->when($team_id, function ($q, $team_id) {
                return $q->where('team_id', $team_id);
            });

            $query->when($finder_officer_id, function ($q, $finder_officer_id) {
                return $q->where('finder_officer_id', $finder_officer_id);
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

            $query->when($start_date, function ($q, $start_date) {
                return $q->whereDate('memo_date', '>=', $start_date);
            });

            $query->when($end_date, function ($q, $end_date) {
                return $q->whereDate('memo_date', '<=', $end_date);
            });

            $memo_list['memo_list'] = $query->with(['audit_plan:id,plan_no,annual_plan_id','audit_plan.annual_plan:id,project_id,project_name_bn,project_name_en,subject_matter','ac_memo_attachments'])
                ->withCount('memo_logs')
                ->orderBy('parent_office_name_en')
                ->orderBy('cost_center_name_en')
                ->paginate($request->per_page ?: config('bee_config.per_page_pagination'));

            $memo_list['total_memo'] = AcMemo::count('id');

            return ['status' => 'success', 'data' => $memo_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoRecommendationStore(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $audit_memo_recommendaton = new AcMemoRecommendation();
            $audit_memo_recommendaton->memo_id = $request->memo_id;
            $audit_memo_recommendaton->audit_recommendation = $request->audit_recommendation;
            $audit_memo_recommendaton->created_by = $cdesk->officer_id;
            $audit_memo_recommendaton->created_by_name_en = $cdesk->officer_en;
            $audit_memo_recommendaton->created_by_name_bn = $cdesk->officer_bn;
            $audit_memo_recommendaton->save();

            AcMemo::where('id', $request->memo_id)->update(['audit_recommendation' => $request->audit_recommendation]);

            \DB::commit();
            return ['status' => 'success', 'data' => 'Memo Recommendation Successfully'];

        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function responseOfRpuMemo(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        DB::beginTransaction();
        try {

            AcMemo::where('id', $request->memo_id)->update(['response_of_rpu' => $request->response_of_rpu]);
            $apottiItem = ApottiItem::where('memo_id', $request->memo_id)->get();
            if (count($apottiItem) > 0) {
                ApottiItem::where('memo_id', $request->memo_id)->update(['response_of_rpu' => $request->response_of_rpu]);
                $onucchedItem = ApottiItem::where('memo_id', $request->memo_id)->first();
                Apotti::where('id', $onucchedItem->apotti_id)->update(['response_of_rpu' => $request->response_of_rpu]);
            }

            DB::commit();
            return ['status' => 'success', 'data' => 'Response Send Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function acknowledgmentOfRpuMemo(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ac_memo = AcMemo::find($request->memo_id);
            $ac_memo->rpu_acceptor_officer_id = $request->rpu_acceptor_officer_id;
            $ac_memo->rpu_acceptor_officer_name_bn = $request->rpu_acceptor_officer_name_bn;
            $ac_memo->rpu_acceptor_officer_name_en = $request->rpu_acceptor_officer_name_en;
            $ac_memo->rpu_acceptor_unit_name_bn = $request->rpu_acceptor_unit_name_bn;
            $ac_memo->rpu_acceptor_unit_name_en = $request->rpu_acceptor_unit_name_en;
            $ac_memo->rpu_acceptor_designation_name_bn = $request->rpu_acceptor_designation_name_bn;
            $ac_memo->rpu_acceptor_designation_name_en = $request->rpu_acceptor_designation_name_en;
            $ac_memo->rpu_acceptor_signature = $request->rpu_acceptor_signature;
            $ac_memo->save();

            return ['status' => 'success', 'data' => 'Response Send Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function auditMemoRecommendationList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $audit_memo_recommendaton_list = AcMemoRecommendation::all();
            return ['status' => 'success', 'data' => $audit_memo_recommendaton_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoLogList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $audit_memo_log_list = AcMemoLog::where('memo_id', $request->memo_id)->paginate(config('bee_config.per_page_pagination'));
            return ['status' => 'success', 'data' => $audit_memo_log_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function attachmentList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        if ($request->has('directorate_id')) {
            $office_db_con_response = $this->switchOffice($request->directorate_id);
        } else {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
        }

        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $data['porisishtos'] = AcMemoAttachment::where('ac_memo_id', $request->memo_id)->where('file_type', 'porisishto')->get()->toArray();
            $data['pramanoks'] = AcMemoAttachment::where('ac_memo_id', $request->memo_id)->where('file_type', 'pramanok')->get()->toArray();
            $data['memos'] = AcMemoAttachment::where('ac_memo_id', $request->memo_id)->where('file_type', 'memo')->get()->toArray();
            return ['status' => 'success', 'data' => $data];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function auditMemoAttachmentDelete(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            AcMemoAttachment::where('id', $request->memo_attachment_id)
                ->update(['deleted_by' => $cdesk->officer_id]);
            AcMemoAttachment::find($request->memo_attachment_id)->delete();
            \DB::commit();
            return ['status' => 'success', 'data' => 'Attachment Delete Successfully'];

        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function updateAllEntityTransaction(Request $request): array
    {

        try {
//            $directorates = OfficeDomain::where('office_id',4)->pluck('office_id');

            $directorates = OfficeDomain::where('office_id','!=',1)
                ->where('office_id','!=',36)
                ->pluck('office_id');

            foreach ($directorates as $directorate_id){

                $office_db_con_response = $this->switchOffice($directorate_id);

                if (isSuccessResponse($office_db_con_response)) {
                    AcMemo::whereIn('cost_center_id', $request->cost_center_id)
                        ->where('parent_office_id',$request->parent_office_id)
                        ->where('ministry_id', $request->office_ministry_id)
                        ->update([
                            'parent_office_id' => $request->office_id,
                            'parent_office_name_bn' => $request->office_name_bn,
                            'parent_office_name_en' => $request->office_name_en,
                        ]);

//                return ['status' => 'success', 'data' => 1];

                    $apotti = ApottiItem::whereIn('cost_center_id', $request->cost_center_id)
                        ->where('parent_office_id', $request->parent_office_id)
                        ->where('ministry_id', $request->office_ministry_id);

                    $apotti_ids = $apotti->pluck('apotti_id');

//                return ['status' => 'success', 'data' => 1];

                    Apotti::whereIn('id', $apotti_ids)
                        ->where('parent_office_id',$request->parent_office_id)
                        ->update([
                            'parent_office_id' => $request->office_id,
                            'parent_office_name_bn' => $request->office_name_bn,
                            'parent_office_name_en' => $request->office_name_en,
                        ]);

                    $apotti->update([
                        'parent_office_id' => $request->office_id,
                        'parent_office_name_bn' => $request->office_name_bn,
                        'parent_office_name_en' => $request->office_name_en,
                    ]);
                }
            }

            return ['status' => 'success', 'data' => 'Update Successfully'];

        } catch (\Exception $exception) {

            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
