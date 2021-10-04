<?php

namespace App\Services;

use App\Models\AuditPlanTeamInfo;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\XResponsibleOffice;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class MISAndDashboardService
{
    use GenericData;

    public function allTeams(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $team_lists = [];
            $directorates = XResponsibleOffice::select('office_id', 'office_name_bn', 'office_name_en')->where('office_layer', 2)->get();

            foreach ($directorates as $directorate) {
                $team_list = [];
                $office_db_con_response = $this->switchOffice($directorate->office_id);
                if (!isSuccessResponse($office_db_con_response)) {
                    return ['status' => 'error', 'data' => $office_db_con_response];
                }
                $team_members_count = AuditVisitCalenderPlanMember::where('fiscal_year_id', $request->fiscal_year_id)->count();
                $total_teams_count = AuditVisitCalendarPlanTeam::where('fiscal_year_id', $request->fiscal_year_id)->count();

                $team_list['office_name_en'] = $directorate->office_name_en;
                $team_list['office_name_bn'] = $directorate->office_name_bn;
                $team_list['total_teams'] = $total_teams_count;
                $team_list['total_team_members'] = $team_members_count;
                $team_lists[] = $team_list;
                $this->emptyOfficeDBConnection();
            }
            return ['status' => 'success', 'data' => $team_lists];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];

        }
    }

    public function storeAuditPlanTeamInfo(Request $request){
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        $team =  AuditVisitCalendarPlanTeam::selectRaw('count(id) as total_team, SUM(activity_man_days) AS total_working_days,duration_id,outcome_id,output_id')->where('fiscal_year_id',$request->fiscal_year_id)->where('audit_plan_id',$request->audit_plan_id)->first();
        $team_member_count =  AuditVisitCalenderPlanMember::where('fiscal_year_id',$request->fiscal_year_id)->where('audit_plan_id',$request->audit_plan_id)->count();

        $auditPlanTeamInfo = AuditPlanTeamInfo::updateOrCreate(
            ['fiscal_year_id' => $request->fiscal_year_id],
            [
                'duration_id' => $team->duration_id,
                'outcome_id' => $team->outcome_id,
                'output_id' => $team->output_id,
                'directorate_id' => $cdesk->office_id,
                'total_teams' => $team->total_team,
                'total_employees' => 10,
                'total_team_members' => $team_member_count,
                'total_working_days' => $team->total_working_days,

            ]
        );
        $this->emptyOfficeDBConnection();

    }
}
