<?php

namespace App\Services;

use App\Models\ApEntityIndividualAuditPlan;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditVisitCalendarPlanService
{
    use GenericData,ApiHeart;

    public function getVisitPlanCalendar(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $designation['designation_id'] = $cdesk->designation_id;

        $designation_info = $this->initDoptorHttp($cdesk->user_id)->post(config('cag_doptor_api.designation_role'), $designation)->json();
//        return ['status' => 'success', 'data' => $designation_info];
//        dd($designation_info);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            if($designation_info['data']['is_office_admin'] || $designation_info['data']['is_office_head']){
                $calendar = AuditVisitCalendarPlanTeam::with('plan_member')->paginate(20);
            }else{
//               $calendar = AuditVisitCalendarPlanTeam::with('plan_member')->get();
               $calendar = AuditVisitCalenderPlanMember::with('plan_team')->where('team_member_designation_id', $cdesk->designation_id)->where('team_member_officer_id', $cdesk->officer_id)->where('team_member_office_id', $cdesk->office_id)->get();
            }

            return ['status' => 'success', 'data' => $calendar];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function updateVisitCalenderStatus($request)
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
