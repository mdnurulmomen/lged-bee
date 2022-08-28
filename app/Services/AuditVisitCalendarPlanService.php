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

    public function getTeamVisitPlanCalendar(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            if ($cdesk->is_office_admin || $cdesk->is_office_head) {
                $calendar = AuditVisitCalendarPlanTeam::with('child')->where('fiscal_year_id', $request->fiscal_year_id)->where('approve_status', 1)->get()->toArray();
            } else {
                $team_id = AuditVisitCalenderPlanMember::where('fiscal_year_id', $request->fiscal_year_id)->where('team_member_designation_id', $cdesk->designation_id)->where('team_member_officer_id', $cdesk->officer_id)->where('team_member_office_id', $cdesk->office_id)->distinct('team_id')->pluck('team_id');
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

    public function teamCalenderFilter(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $fiscal_year_id = $request->fiscal_year_id;
            $team_id = $request->team_id;
//            $team_id = [$request->team_id];

            if($team_id){
                 $child_team =  AuditVisitCalendarPlanTeam::where('team_parent_id',$request->team_id)
                    ->pluck('id');
                 $team_id[] = $child_team;
                 $team_id[] = $request->team_id;
            }else{
                $team_id = 0;
            }

            $cost_center_id = $request->cost_center_id;

            if ($cdesk->is_office_admin === false && $cdesk->is_office_head === false) {
                $team_id = AuditVisitCalenderPlanMember::where('fiscal_year_id', $request->fiscal_year_id)
                    ->where('team_member_designation_id', $cdesk->designation_id)
                    ->where('team_member_officer_id', $cdesk->officer_id)
                    ->where('team_member_office_id', $cdesk->office_id)
                    ->distinct('team_id')
                    ->pluck('team_id');
            }


            if($cost_center_id && $request->team_id){
                $team_id = [$request->team_id];
            }
            else if($cost_center_id){
                $team_id = AuditVisitCalenderPlanMember::where('fiscal_year_id', $request->fiscal_year_id)
                    ->where('cost_center_id',$cost_center_id)
                    ->distinct('team_id')
                    ->pluck('team_id');
            }


            $query = AuditVisitCalendarPlanTeam::query();

            if (!empty($team_id)) {
//                return ['status' => 'success', 'data' => $team_id];
                $query->whereIn('id', $team_id);
            }else{
                $query->where('team_schedules','!=', 'null');
            }

//            if(!$request->team_id){
//                $query->where('team_parent_id' ,0);
//            }

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });


            $query->where('approve_status', 1);

            $calendar = $query->with('child')->get();

            return ['status' => 'success', 'data' => $calendar];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function fiscalYearWiseTeams(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $auditPlanTeamList = AuditVisitCalendarPlanTeam::with('child')->where('fiscal_year_id', $request->fiscal_year_id)->where('team_parent_id', 0)->get();
            return ['status' => 'success', 'data' => $auditPlanTeamList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function fiscalYearCostCenterWiseTeams(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $team_id = AuditVisitCalenderPlanMember::where('fiscal_year_id', $request->fiscal_year_id)->where('cost_center_id', $request->cost_center_id)->get()->unique('team_parent_id')->pluck('team_parent_id');
            $auditPlanTeamList = AuditVisitCalendarPlanTeam::with('child')->where('fiscal_year_id', $request->fiscal_year_id)->whereIn('id', $team_id)->get();
            return ['status' => 'success', 'data' => $auditPlanTeamList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function scheduleEntityFiscalYearWise(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $query = AuditVisitCalenderPlanMember::query();
            $query->select('entity_id','entity_name_bn','entity_name_en');

            //fiscal_year_id
            $fiscal_year_id = $request->fiscal_year_id;
            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            //activity_id
            $activity_id = $request->activity_id ?? '';
            $query->when($activity_id, function ($q, $activity_id) {
                return $q->where('activity_id', $activity_id);
            });

            $entityList = $query->whereNotNull('entity_id')
                ->distinct('entity_id')
                ->get();

            return ['status' => 'success', 'data' => $entityList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function costCenterAndFiscalYearWiseTeams(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $team_id = AuditVisitCalenderPlanMember::where('cost_center_id',$request->cost_center_id)->distinct('team_id')->pluck('team_id');
            $auditPlanTeamList = AuditVisitCalendarPlanTeam::with('child')->where('fiscal_year_id', $request->fiscal_year_id)->whereIn('id', $team_id)->where('team_parent_id', 0)->get();
            return ['status' => 'success', 'data' => $auditPlanTeamList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getSubTeam(Request $request)
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($request->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $data = AuditVisitCalendarPlanTeam::where('team_parent_id', $request->team_id)->get()->toArray();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getCostCenterDirectorateFiscalYearWise(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $costCenterList = AuditVisitCalenderPlanMember::select('cost_center_id','cost_center_name_en','cost_center_name_bn')
                ->where('entity_id', $request->entity_id)->where('fiscal_year_id', $request->fiscal_year_id)
                ->where('cost_center_id', '!=',null)
                ->distinct('cost_center_id')->get();
            return ['status' => 'success', 'data' => $costCenterList];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];

        }
    }

    public function teamCalenderScheduleList(Request $request): array
    {
        $office_db_con_response = $this->switchOffice($request->office_id);

        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $fiscal_year_id = $request->fiscal_year_id;
            $team_id = $request->team_id;
            $cost_center_id = $request->cost_center_id;

            if($team_id){
                $sub_team_id = AuditVisitCalendarPlanTeam::where('team_parent_id',$team_id)->pluck('id');
            }
//            return ['status' => 'success', 'data' => $sub_team_id];

//            $schedule_list = AuditVisitCalenderPlanMember::whereIn('team_id',$sub_team_id)->where('fiscal_year_id',$fiscal_year_id)->where('cost_center_id',$cost_center_id)->get();

            $query = AuditVisitCalenderPlanMember::query();

            if($team_id){
                $query->whereIn('team_id', $sub_team_id);
            }


//            $query->when($team_id, function ($q, $sub_team_id) {
//                return $q->whereIn('team_id', $sub_team_id);
//            });

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($cost_center_id, function ($q, $cost_center_id) {
                return $q->where('cost_center_id', $cost_center_id);
            });


            $schedule_list = $query->get();

            return ['status' => 'success', 'data' => $schedule_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
