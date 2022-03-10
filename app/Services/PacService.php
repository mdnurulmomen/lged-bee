<?php

namespace App\Services;

use App\Models\AuditTemplate;
use App\Models\PacMeeting;
use App\Models\PacMeetingApotti;
use App\Models\PacMeetingMember;
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

    public function createPacReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $auditTemplate = AuditTemplate::where('template_type', $request->template_type)
                ->where('lang', 'bn')->first()->toArray();
            return ['status' => 'success', 'data' => $auditTemplate];

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

}
