<?php

namespace App\Services;

use App\Models\AcMemoAttachment;
use App\Models\Apotti;
use App\Models\AuditTemplate;
use App\Models\PacApottiDecision;
use App\Models\PacMeeting;
use App\Models\PacMeetingApotti;
use App\Models\PacMeetingApottiDecision;
use App\Models\PacMeetingMember;
use App\Models\PacMeetingWorksheet;
use App\Models\RAir;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
class PacService
{
    use GenericData, ApiHeart;

    public function getPacMeetingList(Request $request): array
    {
        try {
            $meeting_list = PacMeeting::all();
            return ['status' => 'success', 'data' => $meeting_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPacMeetingInfo(Request $request): array
    {
        try {
            $meeting_list = PacMeeting::with('meeting_members')
                ->with('meeting_apottis.pac_meeting_apotti_decisions.pac_apotti_decisions')
                ->with('fiscal_year')
                ->find($request->pac_meeting_id);

            $meeting_apottis = PacMeetingApotti::where('pac_meeting_id',$request->pac_meeting_id)->pluck('apotti_id');

            $office_db_con_response = $this->switchOffice($meeting_list->directorate_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $meeting_apottis = Apotti::whereIn('id',$meeting_apottis)->get();

            $meeting_list['meeting_apotti_details'] = $meeting_apottis;

            $this->emptyOfficeDBConnection();

            return ['status' => 'success', 'data' => $meeting_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function pacMeetingStore(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);

            $report_info = RAir::find($request->final_report);

            $meeting = new PacMeeting();
            $meeting->directorate_id =$request->directorate_id;
            $meeting->directorate_bn  =$request->directorate_bn;
            $meeting->directorate_en  =$request->directorate_en;
            $meeting->fiscal_year_id  =$report_info->fiscal_year_id;
            $meeting->report_number  = $report_info->report_number;
            $meeting->report_name  = $report_info->report_name;
            $meeting->ministry_id  = $report_info->ministry_id;
            $meeting->ministry_name_bn  =$report_info->ministry_name_bn;
            $meeting->ministry_name_en  =$report_info->ministry_name_en;
            $meeting->meeting_no  =$request->meeting_no;
            $meeting->meeting_date  =$request->meeting_date;
            $meeting->parliament_no  =$request->parliament_no;
            $meeting->final_report_id  =$request->final_report;
            $meeting->meeting_place  =$request->meeting_place;
            $meeting->meeting_description  =$request->meeting_description;
            $meeting->created_by  = $cdesk->officer_id;
            $meeting->created_by_bn  =$cdesk->officer_bn;
            $meeting->created_by_en  =$cdesk->officer_en;
            $meeting->save();

            foreach ($request->meeting_members as $member){
                $meeting_member = new PacMeetingMember();
                $meeting_member->pac_meeting_id = $meeting->id;
                $meeting_member->member_office_id = $member['office_id'];
                $meeting_member->member_type = $member['officer_type'];
                $meeting_member->member_id = $member['officer_id'];
                $meeting_member->member_bn = $member['officer_bn'];
                $meeting_member->member_en = $member['officer_en'];
                $meeting_member->member_email = $member['officer_email'];
                $meeting_member->member_mobile = $member['officer_mobile'];
                $meeting_member->designation_id = $member['officer_designation_id'];
                $meeting_member->designation_bn = $member['officer_designation_bn'];
                $meeting_member->designation_en = $member['officer_designation_en'];
                $meeting_member->unit_id = $member['officer_unit_id'];
                $meeting_member->unit_bn = $member['officer_unit_bn'];
                $meeting_member->unit_en = $member['officer_unit_en'];
                $meeting_member->save();
            }


            $office_db_con_response = $this->switchOffice($request->directorate_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            foreach ($request->apottis as $apotti){
                $apotti_info =  Apotti::find($apotti);
                $meeting_apotti = new PacMeetingApotti();
                $meeting_apotti->directorate_id =$request->directorate_id;
                $meeting_apotti->directorate_bn  =$request->directorate_bn;
                $meeting_apotti->directorate_en  =$request->directorate_en;
                $meeting_apotti->pac_meeting_id = $meeting->id;
                $meeting_apotti->final_report_id  =$request->final_report;
                $meeting_apotti->apotti_id  = $apotti;
                $meeting_apotti->onucched_no  = $apotti_info->onucched_no;
                $meeting_apotti->apotti_title  = $apotti_info->apotti_title;
                $meeting_apotti->total_jorito_ortho_poriman  = $apotti_info->total_onishponno_jorito_ortho_poriman;
                $meeting_apotti->total_onishponno_jorito_ortho_poriman  = $apotti_info->total_onishponno_jorito_ortho_poriman;
                $meeting_apotti->total_adjustment_ortho_poriman  = $apotti_info->total_adjustment_ortho_poriman;
                $meeting_apotti->save();
            }

            return ['status' => 'success', 'data' => 'Meeting Save Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function pacMeetingApottiDecisionStore(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);

            $meeting_decison = new PacMeetingApottiDecision();
            $meeting_decison->pac_meeting_id  =$request->pac_meeting_id;
            $meeting_decison->final_report_id  =$request->final_report_id;
            $meeting_decison->apotti_id  =$request->apotti_id;
            $meeting_decison->rp_report  =$request->rp_report;
            $meeting_decison->cag_comment  =$request->cag_comment;
            $meeting_decison->apotti_status  =$request->apotti_status;
            $meeting_decison->decision_last_date  =$request->decision_last_date;
            $meeting_decison->follower_office  =$request->follower_office;
            $meeting_decison->created_by  = $cdesk->officer_id;
            $meeting_decison->created_bn  =$cdesk->officer_bn;
            $meeting_decison->created_en  =$cdesk->officer_en;
            $meeting_decison->save();

            $pac_meeting =  PacMeeting::find($request->pac_meeting_id);
            $pac_meeting->is_alochito = 1;
            $pac_meeting->save();

            PacMeetingApotti::where('pac_meeting_id',$request->pac_meeting_id)
                ->where('final_report_id',$request->final_report_id)
                ->where('apotti_id',$request->apotti_id)
                ->update(['is_alochito' => 1]);

            foreach ($request->pac_decision as $decision){
                $pac_decision = new PacApottiDecision();
                $pac_decision->apotti_decision_id = $meeting_decison->id;
                $pac_decision->pac_decision = $decision;
                $pac_decision->apotti_id = $request->apotti_id;
                $pac_decision->final_report_id = $request->final_report_id;
                $pac_decision->save();
            }

            return ['status' => 'success', 'data' => 'Meeting Decision Submitted Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function sentToPac(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);

            $pac_meeting = PacMeeting::find($request->pac_meeting_id);
            $pac_meeting->is_sent_pac = 1;
            $pac_meeting->save();

            return ['status' => 'success', 'data' => 'সফলভাবে প্রেরণ করা হয়েছে'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function createPacWorksheetReport(Request $request): array
    {
        try {
            $auditTemplate = AuditTemplate::where('template_type', $request->template_type)
                ->where('lang', 'bn')->first()->toArray();
            return ['status' => 'success', 'data' => $auditTemplate];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storePacWorksheetReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $reportData = [
                'pac_meeting_id' => $request->fiscal_year_id,
                /*'worksheet_name' => $request->worksheet_name,*/
                'worksheet_description' => $request->worksheet_description,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];
            if ($request->pac_meeting_id) {
                PacMeetingWorksheet::where('id', $request->air_id)->update($reportData);
                $pac_meeting_worksheet_id = $request->pac_meeting_worksheet_id;
            } else {
                $storePacMeetingWorksheet = PacMeetingWorksheet::create($reportData);
                $pac_meeting_worksheet_id = $storePacMeetingWorksheet->id;
            }
            return ['status' => 'success', 'data' => ['pac_meeting_worksheet_id' => $pac_meeting_worksheet_id]];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPACDashboardData(Request $request): array
    {
        try {
            $data['total_report'] = PacMeeting::count();
            $data['total_alochito_report'] = PacMeeting::where('is_alochito', 1)->count();
            $data['total_onalochito_report'] = PacMeeting::where('is_alochito', 0)->count();
            $data['total_alochito_apotti'] = PacMeetingApotti::where('is_alochito', 1)->count();
            $data['total_onalochito_apotti'] = PacMeetingApotti::where('is_alochito', 0)->count();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPACFinalReport(Request $request): array
    {
        try {
            $reports = PacMeeting::with(['fiscal_year']);
            if (!empty($request->ministry_id)){
                $reports = $reports->where('ministry_id', $request->ministry_id);
            }

            if ($request->report_type == 1){
                $reports = $reports->where('is_alochito', 1);
            }elseif ($request->report_type == 0){
                $reports = $reports->where('is_alochito', 0);
            }

            $reports = $reports->get();
            return ['status' => 'success', 'data' => $reports];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function showPACFinalReport(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->directorate_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $data['pac_meeting'] = PacMeeting::with(['fiscal_year'])->find($request->pac_meeting_id)->toArray();
            $data['final_report'] = RAir::with(['reported_apotti_attachments'])->find($request->final_report_id)->toArray();
            return ['status' => 'success', 'data' => $data];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPACApottiList(Request $request): array
    {
        try {
            $limit = $request->length;
            $start = $request->start;
            $order = $request->order;
            $dir = $request->dir;
            $search = $request->search;

            $apottiList = PacMeetingApotti::with(['pac_meeting','pac_meeting.fiscal_year'])
                        ->where('is_alochito',$request->is_alochito);

            $apottiList = $apottiList->where(function ($query) use ($search){
                $query->where('directorate_en',$search)
                    ->orWhere('directorate_bn', 'LIKE',"%{$search}%")
                    ->orWhere('apotti_title', 'LIKE',"%{$search}%")
                    ->orWhere('total_jorito_ortho_poriman', 'LIKE',"%{$search}%")
                    ->orWhere('total_onishponno_jorito_ortho_poriman', 'LIKE',"%{$search}%")
                    ->orWhere('total_adjustment_ortho_poriman', 'LIKE',"%{$search}%");
            });

            $totalData = $apottiList->count();
            $apottiList = $apottiList->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $response = array(
                "apotti_list"=> $apottiList,
                "total_data"=> $totalData,
                "total_filtered"=>$totalData
            );
            return ['status' => 'success', 'data' => $response];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function showPACApotti(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->directorate_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $apotti = Apotti::with(['apotti_items'])->find($request->apotti_id)->toArray();
            $acMemoAttachment = [];
            foreach ($apotti['apotti_items'] as $apotti_items){
                $acMemoAttachment = AcMemoAttachment::where('ac_memo_id',$apotti_items['memo_id'])->get()->toArray();
            }
            $data['apotti'] = $apotti;
            $data['attachments'] = $acMemoAttachment;
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPACMinistry(Request $request): array
    {
        try {
            $ministries = PacMeeting::select('ministry_id','ministry_name_bn')->distinct()->get();
            return ['status' => 'success', 'data' => $ministries];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
