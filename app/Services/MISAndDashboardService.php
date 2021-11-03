<?php

namespace App\Services;

use App\Models\AuditPlanTeamInfo;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class MISAndDashboardService
{
    use GenericData, ApiHeart;

    public function allTeams(Request $request): array
    {
        try {
            $auditPlanTeamInfo = AuditPlanTeamInfo::where('fiscal_year_id', $request->fiscal_year_id)->get();
            return ['status' => 'success', 'data' => $auditPlanTeamInfo];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];

        }
    }


    public function storeAuditPlanTeamInfo(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $total_team = AuditVisitCalendarPlanTeam::where('fiscal_year_id', $request->fiscal_year_id)->where('audit_plan_id', $request->audit_plan_id)->count();
            $total_activity_man_days = AuditVisitCalendarPlanTeam::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('audit_plan_id', $request->audit_plan_id)
                ->sum('activity_man_days');
            $team_member_count = AuditVisitCalenderPlanMember::where('fiscal_year_id', $request->fiscal_year_id)->where('audit_plan_id', $request->audit_plan_id)->count();
            $this->emptyOfficeDBConnection();

            $total_resources = $this->initDoptorHttp($cdesk->user_primary_id)->post(config('cag_doptor_api.office_employees'), ['office_id' => $cdesk->office_id, 'type' => 'count'])->json();
            $total_resources = isSuccessResponse($total_resources) ? $total_resources['data'][0]['employees_count'] : 0;

            AuditPlanTeamInfo::updateOrCreate(
                ['fiscal_year_id' => $request->fiscal_year_id,'office_id' => $cdesk->office_id],
                [
                    'duration_id' => 1,
                    'outcome_id' => 1,
                    'output_id' => 1,
                    'office_id' => $cdesk->office_id,
                    'office_name_bn' => $cdesk->office_name_bn,
                    'office_name_en' => $cdesk->office_name_en,
                    'total_teams' => $total_team,
                    'total_employees' => $total_resources,
                    'total_team_members' => $team_member_count,
                    'total_working_days' => $total_activity_man_days,
                ]
            );
            return ['status' => 'success', 'data' => 'Successfully Created'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
