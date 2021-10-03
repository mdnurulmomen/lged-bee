<?php

namespace App\Services;

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
}
