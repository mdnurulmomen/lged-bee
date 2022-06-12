<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\ApottiRAirMap;
use App\Models\ApottiStatus;
use App\Models\ArcReport;
use App\Models\ArcReportApotti;
use App\Models\ArcReportAttachment;
use App\Models\PacMeeting;
use App\Models\PacMeetingApotti;
use App\Models\RAir;
use App\Models\ReportedApottiAttachment;
use App\Models\XFiscalYear;
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

    public function list(Request $request)
    {
        try {
            $query = ArcReport::query();

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

            //entity_id
            $entity_id = $request->entity_id;
            $query->when($entity_id, function ($query) use ($entity_id) {
                return $query->where('entity_id', $entity_id);
            });


            //year_from
            $year_from = $request->year_from;
            $query->when($year_from, function ($query) use ($year_from) {
                return $query->where('year_from', $year_from);
            });

            //year_to
            $year_to = $request->year_to;
            $query->when($year_to, function ($query) use ($year_to) {
                return $query->where('year_to', $year_to);
            });

            $apotti_report_list = $query->orderBy('id', 'DESC')->get()->toArray();
            return ['status' => 'success', 'data' => $apotti_report_list];
        } catch (\Exception $exception) {
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
            $arc_report->entity_id = $request->entity_id;
            $arc_report->entity_name_bn = $request->entity_name;
            $arc_report->entity_name_en = $request->entity_name;
            $arc_report->is_alochito = $request->is_alochito;

            if ($request->hasfile('cover_page')) {
                foreach ($request->cover_page as $key => $file) {
                    if ($key == 0) {
                        $fileExtension = $file->extension();
                        $fileName = 'cover_page_' . uniqid() . '.' . $fileExtension;
                        Storage::disk('public')->put('archive/reports/' . $fileName, File::get($file));
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

                    Storage::disk('public')->put('archive/apotti/' . $fileName, File::get($file));
                    array_push(
                        $apotti_attachments,
                        array(
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

    public function view(Request $request)
    {
        try {
            $report = ArcReport::with('archive_apottis','arc_report_attachment')
                ->find($request->report_id);

            return ['status' => 'success', 'data' => $report];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function storeReportApotii(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);

            $arc_report_apotti = $request->id ? ArcReportApotti::find($request->id) : new ArcReportApotti();
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

    public function reportSync(Request $request): array
    {
        try {
            $arc_report =  ArcReport::with('archive_apottis', 'arc_report_attachment')->find($request->report_id);
            $cdesk = json_decode($request->cdesk, false);

            $office_db_con_response = $this->switchOffice($arc_report->directorate_id);

            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $fiscal_year = XFiscalYear::where('start', $arc_report->year_from)->first();
            if ($fiscal_year) {
                $fiscal_year_id = $fiscal_year->id;
                $fiscal_year_desc = $fiscal_year->start . ' - ' . $fiscal_year->end;
            } else {
                $fiscal_year_id = 56;
                $fiscal_year_desc = 'Unidentified Fiscal Year';
            }

            $airData = [
                'report_name' => $arc_report->audit_report_name,
                'ministry_id' => $arc_report->ministry_id,
                'ministry_name_en' => $arc_report->ministry_name_en,
                'ministry_name_bn' => $arc_report->ministry_name_bn,
                'entity_id' => $arc_report->entity_id,
                'entity_name_en' => $arc_report->entity_name_en,
                'entity_name_bn' => $arc_report->entity_name_bn,
                'air_description' => 'undefined',
                'fiscal_year_id' => $fiscal_year_id,
                'activity_id' => 0,
                'annual_plan_id' => 0,
                'audit_plan_id' => 0,
                'air_description' => $fiscal_year_desc,
                'type' => 'cqat',
                'report_type' => 'generated',
                'status' => 'approved',
                'created_by' => 0,
                'modified_by' => 0,
                'is_printing_done' => 1,
                'is_bg_press' => 1,
                'is_alochito' => 1,
                'is_sent' => 1,
                'final_approval_status' => 'approved',
                'has_report_attachments' => 1,
                'archived_report_id' => 0,
            ];

            $air_id =  RAir::create($airData);
            $attachment_data_arr = [];
            foreach ($arc_report['arc_report_attachment'] as $archived_reported_attachment) {
                $attachment_data = [
                    'report_id' => $air_id->id,
                    'attachment_type' => $archived_reported_attachment['attachment_type'],
                    'user_define_name' => $archived_reported_attachment['user_define_name'],
                    'attachment_name' => $archived_reported_attachment['attachment_name'],
                    'attachment_path' => $archived_reported_attachment['attachment_path'],
                    'cover_page_name' => $archived_reported_attachment['cover_page_name'],
                    'cover_page_path' => $archived_reported_attachment['cover_page_path'],
                    'cover_page' => $archived_reported_attachment['cover_page'],
                ];
                $attachment_data_arr[] = $attachment_data;
            }

            ReportedApottiAttachment::insert($attachment_data_arr);

            //            return ['status' => 'success', 'data' => $arc_report['archive_apottis']];
            $apotti_ids = [];

            $rp_ac_memo_data = [];
            $rp_apotti_data = [];
            $rp_apotti_item_data = [];
            foreach ($arc_report['archive_apottis'] as $memo) {
                if ($memo['cost_center_id']) {
                    $fiscal_year = XFiscalYear::where('start', bnToen($memo['orthobosor_start']))->first();
                    if ($fiscal_year) {
                        $fiscal_year_id = $fiscal_year->id;
                        $fiscal_year_desc = $fiscal_year->start . ' - ' . $fiscal_year->end;
                    } else {
                        $fiscal_year_id = 56;
                        $fiscal_year_desc = 'Unidentified Fiscal Year';
                    }

                    if (!$fiscal_year_id) {
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

                    if ($memo['orthobosor_start'] && $memo['orthobosor_end']) {
                        $apotti_year_start = bnToen($memo['orthobosor_start']);
                        $apotti_year_end = bnToen($memo['orthobosor_end']);
                    } else {
                        $apotti_year_start = 2090;
                        $apotti_year_end = 2091;
                    }

                    if ($apotti_year_start < 1970 && $apotti_year_end > 2091) {
                        $apotti_year_start = 2090;
                        $apotti_year_end = 2091;
                    }

                    if (strlen($apotti_year_start) != 4) {
                        $apotti_year_start = 2090;
                    }
                    if (strlen($apotti_year_end) != 4) {
                        $apotti_year_end = 2091;
                    }

                    $apotti_year_end = preg_replace("/[^0-9]/", "", $apotti_year_end);
                    $apotti_year_start = preg_replace("/[^0-9]/", "", $apotti_year_start);

                    $cost_center_id = $memo['cost_center_id'];
                    $cost_center_name_en = $memo['cost_center_name_en'];
                    $cost_center_name_bn = $memo['cost_center_name_bn'];

                    $memo_title = $memo['onucched_no_apotti_title'] ?: 'NULL TITLE';

                    $office_ac_memo_data = [
                        'onucched_no' => $memo['onucched_no'],
                        'ac_query_potro_no' => 0,
                        'team_id' => 0,

                        'ministry_id' => $memo['ministry_id'] ?: 0,
                        'ministry_name_en' => $memo['ministry_name_en'] ?: 'undefined ministry',
                        'ministry_name_bn' => $memo['ministry_name_bn'] ?: 'undefined ministry',
                        'parent_office_id' => $memo['parent_office_id'] ?: 0,
                        'parent_office_name_en' => $memo['parent_office_name_en'] ?: 'undefined parent',
                        'parent_office_name_bn' => $memo['parent_office_name_bn'] ?: 'undefined parent',
                        'cost_center_id' => $cost_center_id,
                        'cost_center_name_en' => $cost_center_name_en,
                        'cost_center_name_bn' => $cost_center_name_bn,

                        'report_type_id' => 0,
                        'memo_date' => now()->format('Y-m-d'),

                        'memo_irregularity_type' => 0,
                        'memo_irregularity_sub_type' => 0,
                        'fiscal_year_id' => $fiscal_year_id,
                        'fiscal_year' => $fiscal_year_desc,
                        'ap_office_order_id' => 0,
                        'audit_plan_id' => 0,
                        'audit_year_start' => $apotti_year_start,
                        'audit_year_end' => $apotti_year_end,
                        'audit_type' => $memo['nirikkha_dhoron'] ? $audit_type[$memo['nirikkha_dhoron']] : 'NULL TYPE',
                        'memo_title_bn' => $memo_title,
                        'irregularity_cause' => 0,
                        'memo_type' => 0,
                        'memo_status' => $memo['apotti_status'] ? $memo_status_list[$memo['apotti_status']] : '0',
                        'jorito_ortho_poriman' => bnToen($memo['jorito_ortho_poriman']),
                        'onishponno_jorito_ortho_poriman' => 0,
                        'created_by' => 0,
                        'created_at' => $memo['created_at'],
                        'updated_at' => $memo['updated_at'],
                        'approve_status' => 'draft',
                        'status' => 'draft',
                        'is_archived' => 1,
                        'is_reported' => 1,
                    ];

                    $office_ac_memo = AcMemo::create($office_ac_memo_data);

                    $rp_ac_memo_data[] = $office_ac_memo->toArray();


                    //Apottis
                    $office_apottis = [
                        'audit_plan_id' => 0,
                        'onucched_no' => $memo['onucched_no'],
                        'apotti_title' => $office_ac_memo->memo_title_bn,
                        'apotti_description' => '',
                        'ministry_id' => $memo['ministry_id'] ?: 0,
                        'ministry_name_en' => $memo['ministry_name_en'] ?: 'undefined ministry',
                        'ministry_name_bn' => $memo['ministry_name_bn'] ?: 'undefined ministry',
                        'parent_office_id' => $memo['parent_office_id'] ?: 0,
                        'parent_office_name_en' => $memo['parent_office_name_en'] ?: 'undefined parent',
                        'parent_office_name_bn' => $memo['parent_office_name_bn'] ?: 'undefined parent',

                        'fiscal_year_id' => $fiscal_year_id,
                        'total_jorito_ortho_poriman' => $office_ac_memo->jorito_ortho_poriman,
                        'total_onishponno_jorito_ortho_poriman' => $office_ac_memo->onishponno_jorito_ortho_poriman,
                        'created_by' => 0,
                        'approve_status' => 1,
                        'status' => 0,
                        'apotti_sequence' => 1,
                        'is_combined' => 0,
                        'created_at' => $memo['created_at'],
                        'updated_at' => $memo['updated_at'],
                        'is_reported' => 1,
                    ];

                    $apotti = Apotti::create($office_apottis);
                    $rp_apotti_data[] = $apotti->toArray();

                    ApottiStatus::create(
                        [
                            'apotti_id' => $apotti->id,
                            'apotti_type' => 'approved',
                            'qac_type' => 'cqat',
                            'is_same_porishisto' => 0,
                            'is_rules_and_regulation' => 0,
                            'is_imperfection' => 0,
                            'is_risk_analysis' => 0,
                            'is_broadsheet_response' => 0,
                            'created_by' => 0,
                            'created_by_name_en' => 'archive',
                            'created_by_name_bn' => 'archive',
                        ]
                    );

                    $office_apotti_items = [
                        'apotti_id' => $apotti->id,
                        'memo_id' => $office_ac_memo->id,
                        'onucched_no' => 0,
                        'memo_irregularity_type' => $office_ac_memo->memo_irregularity_type,
                        'memo_irregularity_sub_type' => $office_ac_memo->memo_irregularity_sub_type,

                        'ministry_id' => $memo['ministry_id'] ?: 0,
                        'ministry_name_en' => $memo['ministry_name_en'] ?: 'undefined ministry',
                        'ministry_name_bn' => $memo['ministry_name_bn'] ?: 'undefined ministry',
                        'parent_office_id' => $memo['parent_office_id'] ?: 0,
                        'parent_office_name_en' => $memo['parent_office_name_en'] ?: 'undefined parent',
                        'parent_office_name_bn' => $memo['parent_office_name_bn'] ?: 'undefined parent',
                        'cost_center_id' => $cost_center_id,
                        'cost_center_name_en' => $cost_center_name_en,
                        'cost_center_name_bn' => $cost_center_name_bn,

                        'fiscal_year_id' => $fiscal_year_id,
                        'audit_year_start' => $apotti_year_start,
                        'audit_year_end' => $apotti_year_end,
                        'ac_query_potro_no' => 0,
                        'ap_office_order_id' => 0,
                        'audit_plan_id' => 0,
                        'audit_type' => $office_ac_memo->audit_type,
                        'team_id' => 0,
                        'memo_description_bn' => '',
                        'memo_title_bn' => $memo_title,
                        'memo_type' => $office_ac_memo->apottir_dhoron ?: 'NULL TYPE',
                        'memo_status' => $office_ac_memo->memo_status,
                        'jorito_ortho_poriman' => $office_ac_memo->jorito_ortho_poriman,
                        'onishponno_jorito_ortho_poriman' => $office_ac_memo->onishponno_jorito_ortho_poriman,
                        'created_by' => 0,
                        'status' => 0,
                        'created_at' => $memo['created_at'],
                        'updated_at' => $memo['updated_at'],
                    ];
                    $apotti_item = ApottiItem::create($office_apotti_items);
                    $rp_apotti_item_data[] = $apotti_item->toArray();

                    $apotti_ids[] = $apotti->id;
                }
            }

            if (count($apotti_ids) > 0) {
                $apotti_air_map = [];
                foreach ($apotti_ids as $apotti_id) {
                    $apotti_air_map[] = [
                        'apotti_id' => $apotti_id,
                        'rairs_id' => $air_id->id,
                    ];
                }

                ApottiRAirMap::insert($apotti_air_map);

                //CORE DB
                $meeting = new PacMeeting();
                $meeting->directorate_id = $arc_report->directorate_id;
                $meeting->directorate_bn = $arc_report->directorate_bn;
                $meeting->directorate_en = $arc_report->directorate_en;
                $meeting->fiscal_year_id = $fiscal_year_id;
                $meeting->report_number = 0;
                $meeting->report_name = $arc_report->report_name;
                $meeting->ministry_id = $arc_report->ministry_id;
                $meeting->ministry_name_bn = $arc_report->ministry_name_bn;
                $meeting->ministry_name_en = $arc_report->ministry_name_en;
                $meeting->meeting_no = 0;
                $meeting->meeting_date = now()->format('Y-m-d');
                $meeting->parliament_no = 0;
                $meeting->final_report_id = $arc_report->id;
                $meeting->is_alochito = $arc_report->is_alochito ? $arc_report->is_alochito : 0;
                $meeting->meeting_place = 'undefined';
                $meeting->created_by = 0;
                $meeting->created_by_bn = 'archive';
                $meeting->created_by_en = 'archive';
                $meeting->save();

                foreach ($apotti_ids as $apotti) {
                    $apotti_info = Apotti::find($apotti);
                    $meeting_apotti = new PacMeetingApotti();
                    $meeting_apotti->directorate_id = $arc_report->directorate_id;
                    $meeting_apotti->directorate_bn = $arc_report->directorate_bn;
                    $meeting_apotti->directorate_en = $arc_report->directorate_en;
                    $meeting_apotti->pac_meeting_id = $meeting->id;
                    $meeting_apotti->final_report_id = $arc_report->id;
                    $meeting_apotti->apotti_id = $apotti;
                    $meeting_apotti->onucched_no = $apotti_info->onucched_no;
                    $meeting_apotti->apotti_title = $apotti_info->apotti_title;
                    $meeting_apotti->total_jorito_ortho_poriman = $apotti_info->total_onishponno_jorito_ortho_poriman;
                    $meeting_apotti->total_onishponno_jorito_ortho_poriman = $apotti_info->total_onishponno_jorito_ortho_poriman;
                    $meeting_apotti->total_adjustment_ortho_poriman = $apotti_info->total_adjustment_ortho_poriman;
                    $meeting_apotti->save();
                }
            }

            $directorate = $this->initDoptorHttp($cdesk->user_primary_id)->post(config('cag_doptor_api.offices'), ['office_ids' => $arc_report->directorate_id])->json();

            if (isSuccess($directorate)) {
                $directorate = $directorate['data'][$memo->directorate_id];
            } else {
                $directorate = [];
            }

            $rpu_migrate = $this->initRPUHttp()->post(config('cag_rpu_api.archive-migrate-apotti-to-rpu'), [
                'directorate' => json_encode($directorate),
                'memo' => json_encode($rp_ac_memo_data),
                'apotti' => json_encode($rp_apotti_data),
                'apotti_items' => json_encode($rp_apotti_item_data),
            ])->json();

            if (isSuccess($rpu_migrate, 'status', 'error')) {
                throw new Exception('RPU ERROR' . ' - ' . json_encode($rpu_migrate));
            }

            return ['status' => 'success', 'data' => 'office Db sync done'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function reportApottiDelete(Request $request)
    {
        try {
            ArcReportApotti::find($request->apotti_id)->delete();
            return ['status' => 'success', 'data' => 'Apotti Delete Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getArcReportApottiInfo(Request $request)
    {
        try {
            $apotti_info = ArcReportApotti::find($request->apotti_id);
            return ['status' => 'success', 'data' => $apotti_info];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


}
