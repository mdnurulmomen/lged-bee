<?php

namespace App\Services;

use App\Models\ApottiCategory;
use App\Models\ArcApotti;
use App\Models\ArcApottiAttachment;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArchiveApottiService
{
    use GenericData, ApiHeart;

    public function list(Request $request){
        try {
            $query = ArcApotti::query();

            //directorate
            $directorate_id = $request->directorate_id;
            $query->when($directorate_id, function ($query) use($directorate_id) {
                return $query->where('directorate_id', $directorate_id);
            });

            //ministry
            $ministry_id = $request->ministry_id;
            $query->when($ministry_id, function ($query) use($ministry_id) {
                return $query->where(' ', $ministry_id);
            });

            //entity
            $entity_id = $request->entity_id;
            $query->when($entity_id, function ($query) use($entity_id) {
                return $query->where('entity_info_id', $entity_id);
            });

            //unit group
            $unit_group_office_id = $request->unit_group_office_id;
            $query->when($unit_group_office_id, function ($query) use($unit_group_office_id) {
                return $query->where('parent_office_id', $unit_group_office_id);
            });

            //cost center
            $cost_center_id = $request->cost_center_id;
            $query->when($cost_center_id, function ($query) use($cost_center_id) {
                return $query->where('cost_center_id', $cost_center_id);
            });

            //apotti_oniyomer_category_child_id
            $apotti_oniyomer_category_child_id = $request->apotti_oniyomer_category_child_id;
            $query->when($apotti_oniyomer_category_child_id, function ($query) use($apotti_oniyomer_category_child_id) {
                return $query->where('apotti_category_id', $apotti_oniyomer_category_child_id);
            });

            //onucched_no
            $onucched_no = $request->onucched_no;
            $query->when($onucched_no, function ($query) use($onucched_no) {
                return $query->where('onucched_no', $onucched_no);
            });

            //audit_year_start
            $audit_year_start = $request->audit_year_start;
            $query->when($audit_year_start, function ($query) use($audit_year_start) {
                return $query->where('year_start', $audit_year_start);
            });

            //audit_year_end
            $audit_year_end = $request->audit_year_end;
            $query->when($audit_year_end, function ($query) use($audit_year_end) {
                return $query->where('year_end', $audit_year_end);
            });

            //nirikkha_dhoron
            $nirikkha_dhoron = $request->nirikkha_dhoron;
            $query->when($nirikkha_dhoron, function ($query) use($nirikkha_dhoron) {
                return $query->where('nirikkha_dhoron', $nirikkha_dhoron);
            });

            //apottir_dhoron
            $apottir_dhoron = $request->apottir_dhoron;
            $query->when($apottir_dhoron, function ($query) use($apottir_dhoron) {
                return $query->where('apottir_dhoron', $apottir_dhoron);
            });

            //jorito_ortho_poriman
            $jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $query->when($jorito_ortho_poriman, function ($query) use($jorito_ortho_poriman) {
                return $query->where('jorito_ortho_poriman', $jorito_ortho_poriman);
            });

            $apotti_list = $query->with(['oniyomer_category'])->get()->toArray();
            return ['status' => 'success', 'data' => $apotti_list];
        }catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getOniyomerCategoryList(): array
    {
        try {
            $categories = ApottiCategory::where('parent_id',0)->whereNull('directorate_id')->get()->toArray();
            return ['status' => 'success', 'data' => $categories];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getParentWiseOniyomerCategory(Request $request): array
    {
        try {
            $categories = ApottiCategory::where('directorate_id',$request->directorate_id)
            ->where('parent_id',$request->apotti_oniyomer_category_id)
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

            $arc_apotti = new ArcApotti();
            $arc_apotti->onucched_no = $request->onucched_no;
            $arc_apotti->apotti_oniyomer_dhoron = $request->apotti_oniyomer_category_child_id;
            $arc_apotti->directorate_id  = $request->directorate_id;
            $arc_apotti->office_ministry_id = $request->ministry_id;

            if ($request->hasfile('cover_page')) {
                foreach ($request->cover_page as $key => $file) {
                    if ($key == 0){
                        $fileExtension = $file->extension();
                        $fileName = 'cover_page_' . uniqid() . '.' . $fileExtension;
                        Storage::disk('public')->put('archive/apotti/'. $fileName, File::get($file));
                        $arc_apotti->cover_page = $fileName;
                        $arc_apotti->cover_page_path  = 'storage/archive/apotti/';
                        $arc_apotti->attachment_path  = 'storage/archive/apotti/';
                    }
                }
            }

            $arc_apotti->cost_center_id  = $request->cost_center_id;
            $arc_apotti->cost_center_name_en  = $request->cost_center_name_bn;
            $arc_apotti->cost_center_name_bn  = $request->cost_center_name_bn;
            $arc_apotti->apotti_year = $request->audit_year_start.'-'.$request->audit_year_end;
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
            $arc_apotti->nirikkhar_shal = $request->audit_year_start.'-'.$request->audit_year_end;
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

                    Storage::disk('public')->put('archive/apotti/'. $fileName, File::get($file));
                    array_push($finalAttachments, array(
                            'apotti_id' => $arc_apotti->id,
                            'attachment_type' => 'top_page',
                            'user_define_name' => $userDefineFileName,
                            'attachment_name' => $fileName,
                            'attachment_path' => 'storage/archive/apotti/',
                        )
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

                    Storage::disk('public')->put('archive/apotti/'. $fileName, File::get($file));
                    array_push($finalAttachments, array(
                            'apotti_id' => $arc_apotti->id,
                            'attachment_type' => 'main',
                            'user_define_name' => $userDefineFileName,
                            'attachment_name' => $fileName,
                            'attachment_path' => 'storage/archive/apotti/',
                        )
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

                    Storage::disk('public')->put('archive/apotti/'. $fileName, File::get($file));
                    array_push($finalAttachments, array(
                            'apotti_id' => $arc_apotti->id,
                            'attachment_type' => 'porisishto',
                            'user_define_name' => $userDefineFileName,
                            'attachment_name' => $fileName,
                            'attachment_path' => 'storage/archive/apotti/',
                        )
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

                    Storage::disk('public')->put('archive/apotti/'. $fileName, File::get($file));
                    array_push($finalAttachments, array(
                            'apotti_id' => $arc_apotti->id,
                            'attachment_type' => 'promanok',
                            'user_define_name' => $userDefineFileName,
                            'attachment_name' => $fileName,
                            'attachment_path' => 'storage/archive/apotti/',
                        )
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

                    Storage::disk('public')->put('archive/apotti/'. $fileName, File::get($file));
                    array_push($finalAttachments, array(
                            'apotti_id' => $arc_apotti->id,
                            'attachment_type' => 'other',
                            'user_define_name' => $userDefineFileName,
                            'attachment_name' => $fileName,
                            'attachment_path' => 'storage/archive/apotti/',
                        )
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

    public function update(Request $request){
        return ['status' => 'error', 'data' => []];
    }

    public function edit(Request $request){
        try {
            $apotti = ArcApotti::with(['oniyomer_category','attachments'])
                ->where('id', $request->apotti_id)
                ->first()
                ->toArray();
            return ['status' => 'success', 'data' => $apotti];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

}
