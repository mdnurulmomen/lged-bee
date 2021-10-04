<?php

namespace App\Services;

use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditVisitCalendarPlanService
{
    use GenericData, ApiHeart;

    public function getVisitPlanCalendar(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            if ($cdesk->is_office_admin || $cdesk->is_office_head) {
                $calendar = AuditVisitCalendarPlanTeam::with('plan_member')->paginate(20);
            } else {
                $calendar = AuditVisitCalenderPlanMember::with('plan_team')->where('team_member_designation_id', $cdesk->designation_id)->where('team_member_officer_id', $cdesk->officer_id)->where('team_member_office_id', $cdesk->office_id)->get();
            }

            return ['status' => 'success', 'data' => $calendar];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getIndividualVisitPlanCalendar(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            if ($cdesk->is_office_admin || $cdesk->is_office_head) {
                $calendar = AuditVisitCalendarPlanTeam::with('child')->where('approve_status', 1)->get()->toArray();
            } else {
                $team_id = AuditVisitCalenderPlanMember::where('team_member_designation_id', $cdesk->designation_id)->where('team_member_officer_id', $cdesk->officer_id)->where('team_member_office_id', $cdesk->office_id)->distinct('team_id')->pluck('team_id');
                $calendar = AuditVisitCalendarPlanTeam::with('child')->whereIn('id', $team_id)->where('approve_status', 1)->get()->toArray();
            }
            return ['status' => 'success', 'data' => $calendar];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function updateVisitCalenderStatus($request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        DB::beginTransaction();
        try {

            $audit_plan_data = AuditVisitCalenderPlanMember::find($request->schedule_id);
            $audit_plan_data->status = $request->status;
            $audit_plan_data->save();
            $data = ['status' => 'success', 'data' => 'Staus Update Successfully'];
        } catch (\Exception $exception) {
            DB::rollBack();
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        DB::commit();
        $this->emptyOfficeDBConnection();
        return $data;
    }
}
