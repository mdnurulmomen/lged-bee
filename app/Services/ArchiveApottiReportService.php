<?php

namespace App\Services;

use App\Models\ArcReport;
use App\Models\ArcReportApotti;
use App\Models\ArcReportAttachment;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArchiveApottiReportService
{
    use GenericData, ApiHeart;

    public function list(Request $request){
        try {
            $query = ArcReport::query();

            //directorate
            $directorate_id = $request->directorate_id;
            $query->when($directorate_id, function ($query) use($directorate_id) {
                return $query->where('directorate_id', $directorate_id);
            });

            //ministry
            $ministry_id = $request->ministry_id;
            $query->when($ministry_id, function ($query) use($ministry_id) {
                return $query->where('ministry_id', $ministry_id);
            });


            //year_from
            $year_from = $request->year_from;
            $query->when($year_from, function ($query) use($year_from) {
                return $query->where('year_from', $year_from);
            });

            //year_to
            $year_to = $request->year_to;
            $query->when($year_to, function ($query) use($year_to) {
                return $query->where('year_to', $year_to);
            });

            $apotti_report_list = $query->orderBy('id','DESC')->get()->toArray();
            return ['status' => 'success', 'data' => $apotti_report_list];
        }catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function store(Request $request): array
    {
        \DB::beginTransaction();
        try {
            $cdesk = json_decode($request->cdesk, false);

            $arc_report = new ArcReport();
            $arc_report->audit_report_name = $request->audit_report_name;
            $arc_report->year_from = $request->year_from;
            $arc_report->year_to  = $request->year_to;
            $arc_report->ortho_bochor  = $request->ortho_bochor;
            $arc_report->directorate_id = $request->directorate_id;
            $arc_report->directorate_en = $request->directorate_name;
            $arc_report->directorate_bn = $request->directorate_name;
            $arc_report->ministry_id = $request->ministry_id;
            $arc_report->ministry_id = $request->ministry_id;
            $arc_report->ministry_name_bn = $request->ministry_name;
            $arc_report->ministry_name_en = $request->ministry_name;
            $arc_report->is_alochito = $request->is_alochito;

            if ($request->hasfile('cover_page')) {
                foreach ($request->cover_page as $key => $file) {
                    if ($key == 0){
                        $fileExtension = $file->extension();
                        $fileName = 'cover_page_' . uniqid() . '.' . $fileExtension;
                        Storage::disk('public')->put('archive/reports/'. $fileName, File::get($file));
                        $arc_report->cover_page = $fileName;
                        $arc_report->cover_page_path  = url('storage/archive/reports/');
                    }
                }
            }
            $arc_report->created_by  = $cdesk->officer_id;
            $arc_report->save();

            //for attachments
            $apotti_attachments = [];

            //for others
            if ($request->hasfile('apottis')) {
                foreach ($request->apottis as $key => $file) {
                    $userDefineFileName = $file->getClientOriginalName();
                    $fileExtension = $file->extension();
                    $fileSize = $file->getSize();
                    $fileName = 'other_' . uniqid() . '.' . $fileExtension;

                    Storage::disk('public')->put('archive/apotti/'. $fileName, File::get($file));
                    array_push($apotti_attachments, array(
                            'report_id' => $arc_report->id,
                            'attachment_type' => 'apotti',
                            'user_define_name' => $userDefineFileName,
                            'attachment_name' => $fileName,
                            'attachment_path' => url('storage/archive/apotti/'),
                        )
                    );
                }
            }

            if (!empty($apotti_attachments)) {
                ArcReportAttachment::insert($apotti_attachments);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'Apotti Saved Successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function view(Request $request){
        try {
            $report = ArcReport::where('id', $request->report_id)->first()->toArray();
            return ['status' => 'success', 'data' => $report];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function storeReportApotii(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);

            $arc_report_apotti = new ArcReportApotti();
            $arc_report_apotti->report_id = $request->report_id;
            $arc_report_apotti->audit_report_name = $request->audit_report_name;
            $arc_report_apotti->directorate_id = $request->directorate_id;
            $arc_report_apotti->directorate_name_en = $request->directorate_name;
            $arc_report_apotti->directorate_name_bn = $request->directorate_name;
            $arc_report_apotti->ministry_id = $request->ministry_id;
            $arc_report_apotti->ministry_name_en = $request->ministry_name;
            $arc_report_apotti->ministry_name_bn = $request->ministry_name;
            $arc_report_apotti->entity_id = $request->entity_id;
            $arc_report_apotti->entity_name_en = $request->entity_name;
            $arc_report_apotti->entity_name_bn = $request->entity_name;
            $arc_report_apotti->parent_office_id = $request->parent_office_id;
            $arc_report_apotti->parent_office_name_en = $request->parent_office_name;
            $arc_report_apotti->parent_office_name_bn = $request->parent_office_name;
            $arc_report_apotti->cost_center_id = $request->cost_center_id;
            $arc_report_apotti->cost_center_name_en = $request->cost_center_name;
            $arc_report_apotti->cost_center_name_bn = $request->cost_center_name;
            $arc_report_apotti->nirikkhito_ortho_bosor = $request->nirikkhito_ortho_bosor;
            $arc_report_apotti->orthobosor_start = $request->orthobosor_start;
            $arc_report_apotti->orthobosor_end = $request->orthobosor_end;
            $arc_report_apotti->nirikkha_dhoron = $request->nirikkha_dhoron;
            $arc_report_apotti->onucched_no = $request->onucched_no;
            $arc_report_apotti->apotti_title = $request->apotti_title;
            $arc_report_apotti->jorito_ortho_poriman = $request->jorito_ortho_poriman;
            $arc_report_apotti->apotti_status = $request->apotti_status;
            $arc_report_apotti->pa_commitee_meeting = $request->pa_commitee_meeting;
            $arc_report_apotti->pa_commitee_siddhanto = $request->pa_commitee_siddhanto;
            $arc_report_apotti->ministry_actions = $request->ministry_actions;
            $arc_report_apotti->audit_department_actions = $request->audit_department_actions;
            $arc_report_apotti->is_nispottikrito = $request->is_nispottikrito;
            $arc_report_apotti->is_alocito = $request->is_alocito;
            $arc_report_apotti->created_by  = $cdesk->officer_id;
            $arc_report_apotti->save();
            return ['status' => 'success', 'data' => 'Apotti Saved Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

}
