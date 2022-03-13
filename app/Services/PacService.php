<?php

namespace App\Services;

use App\Models\AcMemoAttachment;
use App\Models\Apotti;
use App\Models\AuditTemplate;
use App\Models\PacMeeting;
use App\Models\PacMeetingApotti;
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

    public function pacMeetingStore(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);

            $meeting = new PacMeeting();
            $meeting->directorate_id =$request->directorate_id;
            $meeting->directorate_bn  =$request->directorate_bn;
            $meeting->directorate_en  =$request->directorate_en;
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

            foreach ($request->apottis as $apotti){
                $meeting_apotti = new PacMeetingApotti();
                $meeting_apotti->pac_meeting_id = $meeting->id;
                $meeting_apotti->final_report_id  =$request->final_report;
                $meeting_apotti->apotti_id  = $apotti;
                $meeting_apotti->save();
            }

            return ['status' => 'success', 'data' => 'Meeting Save Successfully'];

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
            if ($request->report_type == 1){
                $reports = PacMeeting::with(['fiscal_year'])->where('is_alochito', 1)->get();
            }elseif ($request->report_type == 0){
                $reports = PacMeeting::with(['fiscal_year'])->where('is_alochito', 0)->get();
            }else{
                $reports = PacMeeting::with(['fiscal_year'])->get();
            }
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


}
