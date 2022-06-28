<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\AcMemoAttachment;
use App\Models\Apotti;
use App\Models\ApottiCategory;
use App\Models\ApottiItem;
use App\Models\ArcApotti;
use App\Models\ArcApottiAttachment;
use App\Models\XFiscalYear;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use DB;
use Exception;
use Illuminate\Support\Arr;

class ArchiveApottiService
{
    use GenericData, ApiHeart;

    public function list(Request $request)
    {
        try {
            $query = ArcApotti::query();

            //directorate
            $directorate_id = $request->directorate_id;
            $query->when($directorate_id, function ($query) use ($directorate_id) {
                return $query->where('directorate_id', $directorate_id);
            });

            //ministry
            $ministry_id = $request->ministry_id;
            $query->when($ministry_id, function ($query) use ($ministry_id) {
                return $query->where('ministry_id', $ministry_id);
            });

            //entity
            $entity_id = $request->entity_id;
            $query->when($entity_id, function ($query) use ($entity_id) {
                return $query->where('entity_info_id', $entity_id);
            });

            //unit group
            $unit_group_office_id = $request->unit_group_office_id;
            $query->when($unit_group_office_id, function ($query) use ($unit_group_office_id) {
                return $query->where('parent_office_id', $unit_group_office_id);
            });

            //cost center
            $cost_center_id = $request->cost_center_id;
            $query->when($cost_center_id, function ($query) use ($cost_center_id) {
                return $query->where('cost_center_id', $cost_center_id);
            });

            //apotti_oniyomer_category_child_id
            $apotti_oniyomer_category_child_id = $request->apotti_oniyomer_category_child_id;
            $query->when($apotti_oniyomer_category_child_id, function ($query) use ($apotti_oniyomer_category_child_id) {
                return $query->where('apotti_category_id', $apotti_oniyomer_category_child_id);
            });

            //onucched_no
            $onucched_no = $request->onucched_no;
            $query->when($onucched_no, function ($query) use ($onucched_no) {
                return $query->where('onucched_no', $onucched_no);
            });

            //audit_year_start
            $audit_year_start = $request->audit_year_start;
            $query->when($audit_year_start, function ($query) use ($audit_year_start) {
                return $query->where('year_start', $audit_year_start);
            });

            //audit_year_end
            $audit_year_end = $request->audit_year_end;
            $query->when($audit_year_end, function ($query) use ($audit_year_end) {
                return $query->where('year_end', $audit_year_end);
            });

            //nirikkha_dhoron
            $nirikkha_dhoron = $request->nirikkha_dhoron;
            $query->when($nirikkha_dhoron, function ($query) use ($nirikkha_dhoron) {
                return $query->where('nirikkha_dhoron', $nirikkha_dhoron);
            });

            //apottir_dhoron
            $apottir_dhoron = $request->apottir_dhoron;
            $query->when($apottir_dhoron, function ($query) use ($apottir_dhoron) {
                return $query->where('apottir_dhoron', $apottir_dhoron);
            });

            //jorito_ortho_poriman
            $jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $query->when($jorito_ortho_poriman, function ($query) use ($jorito_ortho_poriman) {
                return $query->where('jorito_ortho_poriman', $jorito_ortho_poriman);
            });

            $apotti_list = $query->with(['oniyomer_category'])->get()->toArray();
            return ['status' => 'success', 'data' => $apotti_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getOniyomerCategoryList(): array
    {
        try {
            $categories = ApottiCategory::where('parent_id', 0)->whereNull('directorate_id')->get()->toArray();
            return ['status' => 'success', 'data' => $categories];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getParentWiseOniyomerCategory(Request $request): array
    {
        try {
            $categories = ApottiCategory::where('directorate_id', $request->directorate_id)
                ->where('parent_id', $request->apotti_oniyomer_category_id)
                ->get()
                ->toArray();
            return ['status' => 'success', 'data' => $categories];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function store(Request $request): array
    {
        \DB::beginTransaction();
        try {
            $cdesk = json_decode($request->cdesk, false);

            $arc_apotti = $request->id ? ArcApotti::find($request->id) : new ArcApotti();
            $arc_apotti->onucched_no = $request->onucched_no;
            $arc_apotti->apotti_oniyomer_dhoron = $request->apotti_oniyomer_category_child_id;
            $arc_apotti->directorate_id  = $request->directorate_id;
            $arc_apotti->office_ministry_id = $request->ministry_id;

            if ($request->hasfile('cover_page')) {
                foreach ($request->cover_page as $key => $file) {
                    if ($key == 0) {
                        $fileExtension = $file->extension();
                        $fileName = 'cover_page_' . uniqid() . '.' . $fileExtension;
                        Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                        $arc_apotti->cover_page = $fileName;
                        $arc_apotti->cover_page_path  = 'storage/archive/apotti/';
                        $arc_apotti->attachment_path  = 'storage/archive/apotti/';
                    }
                }
            }

            $arc_apotti->cost_center_id  = $request->cost_center_id;
            $arc_apotti->cost_center_name_en  = $request->cost_center_name_bn;
            $arc_apotti->cost_center_name_bn  = $request->cost_center_name_bn;
            $arc_apotti->apotti_year = $request->audit_year_start . '-' . $request->audit_year_end;
            $arc_apotti->apotti_title = $request->apotti_title;
            $arc_apotti->jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $arc_apotti->onisponno_jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $arc_apotti->nirikkha_dhoron = $request->nirikkha_dhoron;
            $arc_apotti->apottir_dhoron = $request->apottir_dhoron;
            $arc_apotti->apotti_status = 'অনিস্পন্ন';
            $arc_apotti->entity_info_id = $request->entity_id;
            $arc_apotti->entity_name = $request->entity_name;
            $arc_apotti->created_by  = $cdesk->officer_id;

            $arc_apotti->apotti_category_id  = $request->apotti_oniyomer_category_id;
            $arc_apotti->apotti_sub_category_id  = $request->apotti_oniyomer_category_child_id;
            $arc_apotti->year_start = $request->audit_year_start;
            $arc_apotti->year_end = $request->audit_year_end;
            $arc_apotti->file_token_no  = $request->file_no;
            $arc_apotti->nirikkhar_shal = $request->audit_year_start . '-' . $request->audit_year_end;
            $arc_apotti->parent_office_id = $request->unit_group_office_id;
            $arc_apotti->parent_office_name_en = $request->parent_office_name_bn;
            $arc_apotti->parent_office_name_bn = $request->parent_office_name_bn;
            $arc_apotti->ministry_id = $request->ministry_id;
            $arc_apotti->ministry_name_en = $request->ministry_name;
            $arc_apotti->ministry_name_bn = $request->ministry_name;
            $arc_apotti->save();

            //for attachments
            $finalAttachments = [];

            //for top page
            if ($request->hasfile('top_page')) {
                foreach ($request->top_page as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'top_page_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'top_page',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            //for main apottis
            if ($request->hasfile('main_apottis')) {
                foreach ($request->main_apottis as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'main_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'main',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            //for porisishtos
            if ($request->hasfile('porisishtos')) {
                foreach ($request->porisishtos as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'porisishto_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'porisishto',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            //for pramanoks
            if ($request->hasfile('promanoks')) {
                foreach ($request->promanoks as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'promanok_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'promanok',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            //for others
            if ($request->hasfile('others')) {
                foreach ($request->others as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'other_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'other',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            if (!empty($finalAttachments)) {
                ArcApottiAttachment::insert($finalAttachments);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'Apotti Saved Successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeNewAttachment(Request $request): array
    {
        \DB::beginTransaction();
        try {
            $cdesk = json_decode($request->cdesk, false);

            $arc_apotti = $request->id ? ArcApotti::find($request->id) : new ArcApotti();

            if ($request->hasfile('cover_page')) {
                foreach ($request->cover_page as $key => $file) {
                    if ($key == 0) {
                        $fileExtension = $file->extension();
                        $fileName = 'cover_page_' . uniqid() . '.' . $fileExtension;
                        Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                        $arc_apotti->cover_page = $fileName;
                        $arc_apotti->cover_page_path  = 'storage/archive/apotti/';
                        $arc_apotti->attachment_path  = 'storage/archive/apotti/';
                    }
                }
            }

            $arc_apotti->updated_by  = $cdesk->officer_id;
            $arc_apotti->save();

            //for attachments
            $finalAttachments = [];

            //for top page
            if ($request->hasfile('top_page')) {
                foreach ($request->top_page as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'top_page_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'top_page',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            //for main apottis
            if ($request->hasfile('main_apottis')) {
                foreach ($request->main_apottis as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'main_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'main',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            //for porisishtos
            if ($request->hasfile('porisishtos')) {
                foreach ($request->porisishtos as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'porisishto_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'porisishto',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            //for pramanoks
            if ($request->hasfile('promanoks')) {
                foreach ($request->promanoks as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'promanok_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'promanok',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            //for others
            if ($request->hasfile('others')) {
                foreach ($request->others as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'other_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    $finalAttachments[] = array(
                        'apotti_id' => $arc_apotti->id,
                        'attachment_type' => 'other',
                        'user_define_name' => $userDefineFileName,
                        'attachment_name' => $fileName,
                        'attachment_path' => 'storage/archive/apotti/',
                    );
                }
            }

            if (!empty($finalAttachments)) {
                ArcApottiAttachment::insert($finalAttachments);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'Apotti Saved Successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function deleteAttachment(Request $request): array
    {
        try {
            ArcApottiAttachment::where('id', $request->attachement_id)->delete();
            return ['status' => 'success', 'data' => 'Deleted successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function update(Request $request)
    {
        return ['status' => 'error', 'data' => []];
    }

    public function edit(Request $request)
    {
        try {
            $data['apotti']= ArcApotti::with(['oniyomer_category'])
                ->where('id', $request->apotti_id)->first()->toArray();

            $data['main_attachments'] = ArcApottiAttachment::where('apotti_id',$request->apotti_id)
                ->where('attachment_type','main')->get()->toArray();

            $data['promanok_attachments'] = ArcApottiAttachment::where('apotti_id',$request->apotti_id)
                ->where('attachment_type','promanok')->get()->toArray();

            $data['porisishto_attachments'] = ArcApottiAttachment::where('apotti_id',$request->apotti_id)
                ->where('attachment_type','porisishto')->get()->toArray();

            $data['other_attachments'] = ArcApottiAttachment::where('apotti_id',$request->apotti_id)
                ->where('attachment_type','other')->get()->toArray();

            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function migrateArchiveApottiToAmms(Request $request)
    {
        DB::beginTransaction();
        try {
            $memo = ArcApotti::with(['attachments'])
                ->where('id', $request->apotti_id)
                ->first();

            $cdesk = json_decode($request->cdesk, false);

            $office_db = $this->switchOffice($memo->directorate_id);

            if (isset($office_db['status']) && $office_db['status'] == 'error') {
                throw new \Exception('office DB not found - ' . $memo->directorate_id);
            }

            $fiscal_year = XFiscalYear::where('start', $memo->year_start)->first();
            if ($fiscal_year) {
                $fiscal_year_id = $fiscal_year->id;
                $fiscal_year_desc = $fiscal_year->start . ' - ' . $fiscal_year->end;
            } else {
                $fiscal_year_id = 56;
                $fiscal_year_desc = 'Unidentified Fiscal Year';
            }

            $audit_type = [
                'কমপ্লায়েন্স অডিট' => 'compliance',
                'পারফরমেন্স অডিট' => 'performance',
                'ফাইন্যান্সিয়াল অডিট' => 'financial',
                'বিশেষ অডিট' => 'special',
                'ইস্যুভিত্তিক অডিট' => 'issue_based',
                'বার্ষিক অডিট' => 'yearly',
            ];

            $apotti_type = [
                'নন-এসএফআই' => 'non-sfi',
                'এসএফআই' => 'sfi',
                'ড্রাফ্ট প্যারা' => 'draft-para',
                'পাণ্ডুলিপি' => 'pandulipi',
            ];

            $memo_status_list = [
                'N/A' => '0',
                'নিস্পন্ন' => '1',
                'অনিস্পন্ন' => '2',
                'আংশিক নিস্পন্ন' => '3',
            ];
            $apotti_year_start = $memo->year_start;
            $apotti_year_end = $memo->year_end;

            $onucched_no = bnToen($memo->onucched_no);

            $jorito_ortho_poriman = bnToen($memo->jorito_ortho_poriman);
            $jorito_ortho_poriman = preg_replace("/[^0-9.]/", "", $jorito_ortho_poriman);
            $jorito_ortho_poriman = $jorito_ortho_poriman ?: 0;
            $jorito_ortho_poriman = preg_match('/\d/', $jorito_ortho_poriman) ? $jorito_ortho_poriman : 0;
            $jorito_ortho_poriman = substr_count($jorito_ortho_poriman, ".") > 1 ? preg_replace("/[^0-9]/", "", $jorito_ortho_poriman) : $jorito_ortho_poriman;

            $onisponno_jorito_ortho_poriman = bnToen($memo->onisponno_jorito_ortho_poriman);
            $onisponno_jorito_ortho_poriman = preg_replace("/[^0-9.]/", "", $onisponno_jorito_ortho_poriman);
            $onisponno_jorito_ortho_poriman = $onisponno_jorito_ortho_poriman ?: 0;
            $onisponno_jorito_ortho_poriman = preg_match('/\d/', $onisponno_jorito_ortho_poriman) ? $onisponno_jorito_ortho_poriman : 0;
            $onisponno_jorito_ortho_poriman = substr_count($onisponno_jorito_ortho_poriman, ".") > 1 ? preg_replace("/[^0-9]/", "", $onisponno_jorito_ortho_poriman) : $onisponno_jorito_ortho_poriman;

            $cost_center_name_en = $memo->cost_center_name_en;
            $cost_center_name_bn = $memo->cost_center_name_bn;

            $memo_title = $memo->apotti_title ?: 'NULL TITLE';

            $office_ac_memo_data = [
                'onucched_no' => $onucched_no,
                'ac_query_potro_no' => 0,
                'team_id' => 0,
                'ministry_id' => $memo->ministry_id ?: 0,
                'ministry_name_en' => $memo->ministry_name_en ?: 'undefined ministry',
                'ministry_name_bn' => $memo->ministry_name_bn ?: 'undefined ministry',
                'parent_office_id' => $memo->parent_office_id ?: 0,
                'parent_office_name_en' => $memo->parent_office_name_en ?: 'undefined parent',
                'parent_office_name_bn' => $memo->parent_office_name_bn ?: 'undefined parent',
                'cost_center_id' => $memo->cost_center_id,
                'cost_center_name_en' => $cost_center_name_en,
                'cost_center_name_bn' => $cost_center_name_bn,
                'cover_page' => $memo->cover_page,
                'file_token_no' => $memo->file_token_no,
                'cover_page_path' => $memo->cover_page_path,
                'attachment_path' => $memo->attachment_path,
                'report_type_id' => $memo->report_type_id,
                'memo_date' => $memo->maked_at,
                'memo_irregularity_type' => $memo->apotti_oniomer_dhoron ?: 0,
                'memo_irregularity_sub_type' => $memo->apotti_oniomer_dhoron ?: 0,
                'fiscal_year_id' => $fiscal_year_id,
                'fiscal_year' => $fiscal_year_desc,
                'ap_office_order_id' => 0,
                'audit_plan_id' => 0,
                'audit_year_start' => $apotti_year_start,
                'audit_year_end' => $apotti_year_end,
                'audit_type' => $memo->nirikkhar_dhoron ? $audit_type[$memo->nirikkhar_dhoron] : 'NULL TYPE',
                'memo_title_bn' => $memo_title,
                'irregularity_cause' => $memo->irregularity_cause,
                'memo_type' => 0,
                'memo_status' => 0,
                'jorito_ortho_poriman' => $jorito_ortho_poriman,
                'onishponno_jorito_ortho_poriman' => $onisponno_jorito_ortho_poriman,
                'created_by' => 0,
                'created_at' => $memo->created_at,
                'updated_at' => $memo->updated_at,
                'approve_status' => 'draft',
                'status' => 'draft',
                'is_archived' => 1,
            ];

            $office_ac_memo_create = AcMemo::create($office_ac_memo_data);
            $office_ac_memo_data['id'] = $office_ac_memo_create->id;

            $archived_memo_attachments = $memo->attachments->toArray();
            $sequence = 0;
            $office_ac_memo_attachment_data = [];
            foreach ($archived_memo_attachments as $archived_memo_attachment) {
                $file_ext = explode('.', $archived_memo_attachment['attachment_name']);
                $file_ext = end($file_ext);
                if (strlen($file_ext) < 3 || strlen($file_ext) > 5) {
                    $file_ext = 'jpg';
                }
                $office_ac_memo_attachment_data[] = [
                    'ac_memo_id' => $office_ac_memo_create->id,
                    'file_type' => $archived_memo_attachment['attachment_type'],
                    'file_user_define_name' => $archived_memo_attachment['attachment_name'],
                    'file_custom_name' => $archived_memo_attachment['attachment_name'],
                    'file_path' => $archived_memo_attachment['attachment_path'],
                    'file_extension' => $file_ext,
                    'sequence' => $sequence + 1,
                    'created_by' => 0,
                    'modified_by' => 0,
                    'created_at' => $memo->created_at,
                    'updated_at' => $memo->updated_at,
                ];
            }

            //memo attachment insert
            AcMemoAttachment::insert($office_ac_memo_attachment_data);

            //Apottis
            $office_apottis = [
                'audit_plan_id' => Arr::has($office_ac_memo_data, 'audit_plan_id') ? $office_ac_memo_data['audit_plan_id'] : 0,
                'onucched_no' => Arr::has($office_ac_memo_data, 'onucched_no') ? $office_ac_memo_data['onucched_no'] : '',
                'apotti_title' => Arr::has($office_ac_memo_data, 'memo_title_bn') ? $office_ac_memo_data['memo_title_bn'] : '',
                'apotti_description' => Arr::has($office_ac_memo_data, 'memo_description_bn') ? $office_ac_memo_data['memo_description_bn'] : '',
                'ministry_id' => Arr::has($office_ac_memo_data, 'ministry_id') ? $office_ac_memo_data['ministry_id'] : 0,
                'ministry_name_en' => Arr::has($office_ac_memo_data, 'ministry_name_en') ? $office_ac_memo_data['ministry_name_en'] : '',
                'ministry_name_bn' => Arr::has($office_ac_memo_data, 'ministry_name_en') ? $office_ac_memo_data['ministry_name_en'] : '',
                'parent_office_id' => Arr::has($office_ac_memo_data, 'parent_office_id') ? $office_ac_memo_data['parent_office_id'] : 0,
                'parent_office_name_en' => Arr::has($office_ac_memo_data, 'parent_office_name_en') ? $office_ac_memo_data['parent_office_name_en'] : '',
                'parent_office_name_bn' => Arr::has($office_ac_memo_data, 'parent_office_name_bn') ? $office_ac_memo_data['parent_office_name_bn'] : '',
                'fiscal_year_id' => Arr::has($office_ac_memo_data, 'fiscal_year_id') ? $office_ac_memo_data['fiscal_year_id'] : '',
                'total_jorito_ortho_poriman' => Arr::has($office_ac_memo_data, 'jorito_ortho_poriman') ? $office_ac_memo_data['jorito_ortho_poriman'] : '',
                'total_onishponno_jorito_ortho_poriman' => Arr::has($office_ac_memo_data, 'onishponno_jorito_ortho_poriman') ? $office_ac_memo_data['onishponno_jorito_ortho_poriman'] : '',
                'file_token_no' => Arr::has($office_ac_memo_data, 'file_token_no') ? $office_ac_memo_data['file_token_no'] : null,
                'created_by' => Arr::has($office_ac_memo_data, 'created_by') ? $office_ac_memo_data['created_by'] : '',
                'is_sent_rp' => 1,
                'approve_status' => 1,
                'status' => 0,
                'apotti_sequence' => 1,
                'is_combined' => 0,
                'created_at' => $memo->created_at,
                'updated_at' => $memo->updated_at,
            ];

            $office_apottis_create = Apotti::create($office_apottis);

            $office_apotti_items = [
                'apotti_id' => $office_apottis_create->id,
                'memo_id' => $office_ac_memo_create->id,
                'onucched_no' => Arr::has($office_ac_memo_data, 'onucched_no') ? $office_ac_memo_data['onucched_no'] : '',
                'memo_irregularity_type' => Arr::has($office_ac_memo_data, 'memo_irregularity_type') ? $office_ac_memo_data['memo_irregularity_type'] : '',
                'memo_irregularity_sub_type' => Arr::has($office_ac_memo_data, 'memo_irregularity_sub_type') ? $office_ac_memo_data['memo_irregularity_sub_type'] : '',
                'ministry_id' => Arr::has($office_ac_memo_data, 'ministry_id') ? $office_ac_memo_data['ministry_id'] : '',
                'ministry_name_en' => Arr::has($office_ac_memo_data, 'ministry_name_en') ? $office_ac_memo_data['ministry_name_en'] : '',
                'ministry_name_bn' => Arr::has($office_ac_memo_data, 'ministry_name_en') ? $office_ac_memo_data['ministry_name_en'] : '',
                'parent_office_id' => Arr::has($office_ac_memo_data, 'parent_office_id') ? $office_ac_memo_data['parent_office_id'] : '',
                'parent_office_name_en' => Arr::has($office_ac_memo_data, 'parent_office_name_en') ? $office_ac_memo_data['parent_office_name_en'] : '',
                'parent_office_name_bn' => Arr::has($office_ac_memo_data, 'parent_office_name_bn') ? $office_ac_memo_data['parent_office_name_bn'] : '',
                'cost_center_id' => Arr::has($office_ac_memo_data, 'cost_center_id') ? $office_ac_memo_data['cost_center_id'] : '',
                'cost_center_name_en' => Arr::has($office_ac_memo_data, 'cost_center_name_en') ? $office_ac_memo_data['cost_center_name_en'] : 'undefined cost_center',
                'cost_center_name_bn' => Arr::has($office_ac_memo_data, 'cost_center_name_bn') ? $office_ac_memo_data['cost_center_name_bn'] : 'undefined cost_center',
                'fiscal_year_id' => Arr::has($office_ac_memo_data, 'fiscal_year_id') ? $office_ac_memo_data['fiscal_year_id'] : '',
                'audit_year_start' => Arr::has($office_ac_memo_data, 'audit_year_start') ? $office_ac_memo_data['audit_year_start'] : '',
                'audit_year_end' => Arr::has($office_ac_memo_data, 'audit_year_end') ? $office_ac_memo_data['audit_year_end'] : '',
                'ac_query_potro_no' => Arr::has($office_ac_memo_data, 'ac_query_potro_no') ? $office_ac_memo_data['ac_query_potro_no'] : '',
                'ap_office_order_id' => Arr::has($office_ac_memo_data, 'ap_office_order_id') ? $office_ac_memo_data['ap_office_order_id'] : '',
                'audit_plan_id' => Arr::has($office_ac_memo_data, 'audit_plan_id') ? $office_ac_memo_data['audit_plan_id'] : '',
                'audit_type' => Arr::has($office_ac_memo_data, 'audit_type') ? $office_ac_memo_data['audit_type'] : '',
                'team_id' => Arr::has($office_ac_memo_data, 'team_id') ? $office_ac_memo_data['team_id'] : '',
                'memo_description_bn' => Arr::has($office_ac_memo_data, 'memo_description_bn') ? $office_ac_memo_data['memo_description_bn'] : '',
                'memo_title_bn' => Arr::has($office_ac_memo_data, 'memo_title_bn') ? $office_ac_memo_data['memo_title_bn'] : '',
                'memo_type' => $memo->apottir_dhoron ? $apotti_type[$memo->apottir_dhoron] : 'NULL TYPE',
                'memo_status' => $memo->apotti_status ? $memo_status_list[$memo->apotti_status] : '0',
                'jorito_ortho_poriman' => Arr::has($office_ac_memo_data, 'jorito_ortho_poriman') ? $office_ac_memo_data['jorito_ortho_poriman'] : '',
                'onishponno_jorito_ortho_poriman' => Arr::has($office_ac_memo_data, 'onishponno_jorito_ortho_poriman') ? $office_ac_memo_data['onishponno_jorito_ortho_poriman'] : '',
                'created_by' => Arr::has($office_ac_memo_data, 'created_by') ? $office_ac_memo_data['created_by'] : '',
                'status' => 0,
            ];

            $office_apotti_item_create = ApottiItem::create($office_apotti_items);

            $directorate = $this->initDoptorHttp($cdesk->user_primary_id)->post(config('cag_doptor_api.offices'), ['office_ids' => $memo->directorate_id])->json();

            if (isSuccess($directorate)) {
                $directorate = $directorate['data'][$memo->directorate_id];
            } else {
                $directorate = [];
            }

            $rpu_migrate = $this->initRPUHttp()->post(config('cag_rpu_api.archive-migrate-apotti-to-rpu'), [
                'fiscal_year' => $fiscal_year_desc,
                'memo' => json_encode($office_ac_memo_data),
                'directorate' => json_encode($directorate),
                'onucched_no' => $office_ac_memo_data['onucched_no'],
                'attachments' => json_encode($archived_memo_attachments),
                'apotti' => json_encode($office_apottis_create->toArray()),
                'apotti_item' => json_encode($office_apotti_item_create),
            ])->json();

            if (isSuccess($rpu_migrate, 'status', 'error')) {
                throw new Exception('RPU ERROR' . ' - ' . json_encode($rpu_migrate));
            }

            ArcApotti::with(['attachments'])
                ->where('id', $request->apotti_id)
                ->delete();

            DB::commit();
            return ['status' => 'success', 'data' => $rpu_migrate];
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
