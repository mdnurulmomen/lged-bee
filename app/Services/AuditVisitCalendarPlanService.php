<?php

namespace App\Services;

use App\Models\ApEntityIndividualAuditPlan;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditVisitCalendarPlanService
{
    use GenericData;

    public function getIndividualPlanCalendar(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $calendar = AuditVisitCalenderPlanMember::where('team_member_designation_id', $cdesk->designation_id)->where('team_member_officer_id', $cdesk->officer_id)->where('team_member_office_id', $cdesk->office_id)->get();
            return ['status' => 'success', 'data' => $calendar];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function storeAuditTeamCalendar($audit_plan_id, $cdesk)
    {
        $cdesk = json_decode($cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        DB::beginTransaction();
        try {

            $audit_plan_data = ApEntityIndividualAuditPlan::where('id', $audit_plan_id)->select('id AS audit_plan_id', 'annual_plan_id', 'schedule_id', 'activity_id', 'milestone_id', 'fiscal_year_id')->first();

            $team_data = [
                'activity_id' => $audit_plan_data->activity_id,
                'schedule_id' => $audit_plan_data->schedule_id,
                'milestone_id' => $audit_plan_data->milestone_id,
                'fiscal_year_id' => $audit_plan_data->fiscal_year_id,
                'annual_plan_id' => $audit_plan_data->annual_plan_id,
                'audit_plan_id' => $audit_plan_data->audit_plan_id,
            ];

            $team = AuditVisitCalendarPlanTeam::create($team_data);
            $subTeam = AuditVisitCalendarPlanTeam::create($team_data);
            $data = ['status' => 'success', 'data' => 'Created!'];
        } catch (\Exception $exception) {
            DB::rollBack();
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        DB::commit();
        $this->emptyOfficeDBConnection();
        return $data;
    }
}
