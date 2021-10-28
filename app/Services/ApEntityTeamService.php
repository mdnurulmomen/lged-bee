<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApEntityTeamService
{
    use GenericData;

    public function storeAuditTeam(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $this->switchOffice($cdesk->office_id);

        $annualPlan = AnnualPlan::find($request->annual_plan_id);

        $teams = json_decode($request->teams, true);
        $teams = $teams['teams'];
        try {
            $this->saveAuditTeam($teams, $annualPlan, $request);
            $data = ['status' => 'success', 'data' => 'Successfully Saved Team!'];
        } catch (\Exception $e) {
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();

        return $data;
    }

    public function saveAuditTeam($teams, $annualPlan, Request $request)
    {
        try {
            $parent_id = 0;
            foreach ($teams['all_teams'] as $team) {
                if (count($teams['all_teams']) == 1) {
                    $members = json_encode($team['members'], JSON_UNESCAPED_UNICODE);
                } else {
                    $members = $parent_id == 0 ? json_encode($team['members'], JSON_UNESCAPED_UNICODE) : json_encode(['teamLeader' => [$teams['leader']['officer_id'] => $teams['leader']]] + $team['members'], JSON_UNESCAPED_UNICODE);
                }
                $auditVisitCalendarPlanTeam = new AuditVisitCalendarPlanTeam;
                $auditVisitCalendarPlanTeam->fiscal_year_id = $annualPlan->fiscal_year_id;
                $auditVisitCalendarPlanTeam->duration_id = $annualPlan->activity->duration_id;
                $auditVisitCalendarPlanTeam->outcome_id = $annualPlan->activity->outcome_id;
                $auditVisitCalendarPlanTeam->output_id = $annualPlan->activity->output_id;
                $auditVisitCalendarPlanTeam->activity_id = $annualPlan->activity_id;
                $auditVisitCalendarPlanTeam->milestone_id = $annualPlan->milestone_id;
                $auditVisitCalendarPlanTeam->annual_plan_id = $request->annual_plan_id;
                $auditVisitCalendarPlanTeam->audit_year_start = $request->audit_year_start;
                $auditVisitCalendarPlanTeam->audit_year_end = $request->audit_year_end;
                $auditVisitCalendarPlanTeam->audit_plan_id = $request->audit_plan_id;
                $auditVisitCalendarPlanTeam->ministry_id = $annualPlan->ministry_id;
                $auditVisitCalendarPlanTeam->controlling_office_id = $annualPlan->controlling_office_id;
                $auditVisitCalendarPlanTeam->controlling_office_name_en = $annualPlan->controlling_office_en;
                $auditVisitCalendarPlanTeam->controlling_office_name_bn = $annualPlan->controlling_office_bn;
                $auditVisitCalendarPlanTeam->entity_id = $annualPlan->parent_office_id;
                $auditVisitCalendarPlanTeam->entity_name_en = $annualPlan->parent_office_name_en;
                $auditVisitCalendarPlanTeam->entity_name_bn = $annualPlan->parent_office_name_bn;
                $auditVisitCalendarPlanTeam->team_name = $team['team_name'];
                $auditVisitCalendarPlanTeam->team_start_date = $teams['team_start_date'];
                $auditVisitCalendarPlanTeam->team_end_date = $teams['team_end_date'];
                $auditVisitCalendarPlanTeam->team_members = $members;
                $auditVisitCalendarPlanTeam->leader_name_en = $team['leader_name_en'];
                $auditVisitCalendarPlanTeam->leader_name_bn = $team['leader_name_bn'];
                $auditVisitCalendarPlanTeam->leader_designation_id = $team['leader_designation_id'];
                $auditVisitCalendarPlanTeam->leader_designation_name_en = $team['leader_designation_en'];
                $auditVisitCalendarPlanTeam->leader_designation_name_bn = $team['leader_designation_bn'];
                if ($team['team_type'] == 'parent') {
                    $auditVisitCalendarPlanTeam->team_parent_id = 0;
                } else {
                    $auditVisitCalendarPlanTeam->team_parent_id = $parent_id;
                }

                $auditVisitCalendarPlanTeam->activity_man_days = 0;
                $auditVisitCalendarPlanTeam->audit_year_start = $request->audit_year_start;
                $auditVisitCalendarPlanTeam->audit_year_end = $request->audit_year_end;
                $auditVisitCalendarPlanTeam->approve_status = 1;
                $auditVisitCalendarPlanTeam->save();

                if ($team['team_type'] == 'parent') {
                    $parent_id = $auditVisitCalendarPlanTeam->id;
                }
            }

            return ['status' => 'success', 'data' => 'save data successful'];
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function updateAuditTeam(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $this->switchOffice($cdesk->office_id);

        $annualPlan = AnnualPlan::find($request->annual_plan_id);

        $teams = json_decode($request->teams, true);
        $teams = $teams['teams'];
        try {
            AuditVisitCalendarPlanTeam::where('audit_plan_id', $request->audit_plan_id)->delete();
            $this->saveAuditTeam($teams, $annualPlan, $request);
            $data = ['status' => 'success', 'data' => 'Successfully Saved Team!'];
        } catch (\Exception $e) {
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();

        return $data;
    }

    public function storeTeamSchedule(Request $request): array
    {
//        return ['status' => 'error', 'data' => json_decode($request->team_schedules, true)];

        $cdesk = json_decode($request->cdesk, false);
        $this->switchOffice($cdesk->office_id);
        $team_schedules = json_decode($request->team_schedules, true);
        $team_schedules = $team_schedules['schedule'];
        DB::beginTransaction();
        try {
            $this->saveTeamSchedule($team_schedules, $request->audit_plan_id);
            $data = ['status' => 'success', 'data' => 'successfully saved'];
            DB::commit();
            $this->emptyOfficeDBConnection();
            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $e->getMessage()];
        } catch (\Error $e) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $e->getMessage()];
        }
    }

    public function saveTeamSchedule($team_schedules, $audit_plan_id)
    {
        try {
            foreach ($team_schedules as $designation_id => $schedule_data) {
                dump($designation_id);
                
            }
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function updateTeamSchedule(Request $request): array
    {
        //return ['status' => 'error', 'data' => json_decode($request->team_schedules, true)];

        $cdesk = json_decode($request->cdesk, false);
        $this->switchOffice($cdesk->office_id);
        $team_schedules = json_decode($request->team_schedules, true);
        $team_schedules = $team_schedules['schedule'];
        DB::beginTransaction();
        try {
            AuditVisitCalenderPlanMember::where('audit_plan_id', $request->audit_plan_id)->delete();
            $this->saveTeamSchedule($team_schedules, $request->audit_plan_id);
            $data = ['status' => 'success', 'data' => 'successfully saved'];
            DB::commit();
            $this->emptyOfficeDBConnection();
            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $e->getMessage()];
        } catch (\Error $e) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $e->getMessage()];
        }
    }
}
