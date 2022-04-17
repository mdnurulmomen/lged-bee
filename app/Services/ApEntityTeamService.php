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
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        $annualPlan = AnnualPlan::find($request->annual_plan_id);
        if ($request->modal_type == 'data-collection') {
            $annualPlan->has_dc_schedule = 1;
            $annualPlan->save();
        }
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
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        $annualPlan = AnnualPlan::find($request->annual_plan_id);
        if ($request->modal_type == 'data-collection') {
            $annualPlan->has_dc_schedule = 1;
            $annualPlan->save();
        }
        $teams = json_decode($request->teams, true);
        $teams = $teams['teams'];
//        return ['status' => 'success', 'data' => $request->deleted_team];
        try {
//            AuditVisitCalendarPlanTeam::where('audit_plan_id', $request->audit_plan_id)->delete();
//            $this->saveAuditTeam($teams, $annualPlan, $request);

            foreach ($teams['all_teams'] as $team) {
                if (count($teams['all_teams']) == 1) {
                    $members = json_encode($team['members'], JSON_UNESCAPED_UNICODE);
                } else {
                    $members = isset($team['team_parent_id']) && $team['team_parent_id'] == 0 ? json_encode($team['members'], JSON_UNESCAPED_UNICODE) : json_encode(['teamLeader' => [$teams['leader']['officer_id'] => $teams['leader']]] + $team['members'], JSON_UNESCAPED_UNICODE);
                }
                $id = isset($team['id']) &&  $team['id'] ? $team['id'] : null;
                $data = [
                    'team_parent_id' => isset($team['team_parent_id']) && $team['team_parent_id'] == 0 ?  0 : $teams['all_teams'][1]['id'],
                    'fiscal_year_id' => $annualPlan->fiscal_year_id,
                    'duration_id' => $annualPlan->activity->duration_id,
                    'outcome_id' => $annualPlan->activity->outcome_id,
                    'output_id' => $annualPlan->activity->output_id,
                    'activity_id' => $annualPlan->activity_id,
                    'milestone_id' => $annualPlan->milestone_id,
                    'annual_plan_id' => $request->annual_plan_id,
                    'audit_plan_id' => $request->audit_plan_id,
                    'audit_year_start' => $request->audit_year_start,
                    'audit_year_end' => $request->audit_year_end,
                    'team_name' => $team['team_name'],
                    'team_members' => $members,
                    'leader_name_en' => $team['leader_name_en'],
                    'leader_name_bn' => $team['leader_name_bn'],
                    'leader_designation_id' => $team['leader_designation_id'],
                    'leader_designation_name_en' => $team['leader_designation_en'],
                    'leader_designation_name_bn' => $team['leader_designation_bn'],
                    'team_start_date' => $teams['team_start_date'],
                    'team_end_date' => $teams['team_end_date'],
                    'approve_status' => 1,
                ];
                AuditVisitCalendarPlanTeam::updateOrCreate(['id' => $id],$data);
            }

            if($request->deleted_team){
                AuditVisitCalendarPlanTeam::whereIn('id',$request->deleted_team)->delete();
            }

            $data = ['status' => 'success', 'data' => 'successfully updated'];

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
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        $team_schedules = json_decode($request->team_schedules, true);
        $team_schedules = $team_schedules['schedule'];
        DB::beginTransaction();
        try {
            $saveSchedule = $this->saveTeamSchedule($team_schedules, $request->audit_plan_id);
            if (isSuccessResponse($saveSchedule)) {
                $data = ['status' => 'success', 'data' => 'successfully saved'];
            } else {
                throw new \Exception($saveSchedule['data']);
            }
            $this->emptyOfficeDBConnection();
        } catch (\Exception $e) {
            DB::rollBack();
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        } catch (\Error $e) {
            DB::rollBack();
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        DB::commit();
        return $data;

    }

    public function saveTeamSchedule($team_schedules, $audit_plan_id)
    {
        try {
            DB::beginTransaction();
            foreach ($team_schedules as $designation_id => $schedule_data) {
                $team_data = AuditVisitCalendarPlanTeam::where('audit_plan_id', $audit_plan_id)
                    ->where('leader_designation_id', $designation_id)->first();
                if (!$team_data) {
                    throw new \Exception('Team is not formed');
                }
                $team_data->team_schedules = json_encode_unicode($schedule_data);
                $team_data->activity_man_days = array_sum(array_column($schedule_data, 'activity_man_days'));
                $team_data->save();
                foreach ($schedule_data as $schedule_datum) {
                    $team_member = json_decode($team_data->team_members, true);
                    foreach ($team_member as $key => $member_info) {
                        foreach ($member_info as $member) {
                            $team_schedule = [
                                'fiscal_year_id' => $team_data->fiscal_year_id,
                                'team_id' => $team_data->id,
                                'team_parent_id' => $team_data->team_parent_id ?: $team_data->id,
                                'duration_id' => $team_data->duration_id,
                                'outcome_id' => $team_data->outcome_id,
                                'output_id' => $team_data->output_id,
                                'activity_id' => $team_data->activity_id,
                                'milestone_id' => $team_data->milestone_id,
                                'annual_plan_id' => $team_data->annual_plan_id,
                                'audit_plan_id' => $team_data->audit_plan_id,
                                'ministry_id' => empty($schedule_datum['ministry_id']) ? null : $schedule_datum['ministry_id'],
                                'ministry_name_bn' => empty($schedule_datum['ministry_name_bn']) ? null : $schedule_datum['ministry_name_bn'],
                                'ministry_name_en' => empty($schedule_datum['ministry_name_en']) ? null : $schedule_datum['ministry_name_en'],
                                'entity_id' => empty($schedule_datum['entity_id']) ? null : $schedule_datum['entity_id'],
                                'entity_name_en' => empty($schedule_datum['entity_name_en']) ? null : $schedule_datum['entity_name_en'],
                                'entity_name_bn' => empty($schedule_datum['entity_name_bn']) ? null : $schedule_datum['entity_name_bn'],
                                'cost_center_id' => empty($schedule_datum['cost_center_id']) ? null : $schedule_datum['cost_center_id'],
                                'cost_center_name_en' => empty($schedule_datum['cost_center_name_en']) ? null : $schedule_datum['cost_center_name_en'],
                                'cost_center_name_bn' => empty($schedule_datum['cost_center_name_bn']) ? null : $schedule_datum['cost_center_name_bn'],
                                'team_member_name_en' => $member['officer_name_en'],
                                'team_member_name_bn' => $member['officer_name_bn'],
                                'team_member_designation_id' => $member['designation_id'],
                                'team_member_officer_id' => $member['officer_id'],
                                'team_member_office_id' => $member['office_id'],
                                'team_member_designation_en' => $member['designation_en'],
                                'team_member_designation_bn' => $member['designation_bn'],
                                'team_member_role_en' => $member['team_member_role_en'],
                                'team_member_role_bn' => $member['team_member_role_bn'],
                                'team_member_start_date' => empty($schedule_datum['team_member_start_date']) ? null:$schedule_datum['team_member_start_date'],
                                'team_member_end_date' => empty($schedule_datum['team_member_end_date']) ? null:$schedule_datum['team_member_end_date'],
                                //'comment' => '',  //empty($member['comment'])?'':$member['comment'],
                                'mobile_no' => empty($member['officer_mobile']) ? null : $member['officer_mobile'],
                                'employee_grade' => empty($member['employee_grade']) ? null : $member['employee_grade'],
                                'activity_location' => empty($schedule_datum['activity_details']) ? null : $schedule_datum['activity_details'],
                                'sequence_level' => $schedule_datum['sequence_level'],
                                'schedule_type' => $schedule_datum['schedule_type'],
                                'status' => 'pending',
                                'approve_status' => 'approved',
                                'activity_man_days' => empty($schedule_datum['activity_man_days']) ? null : $schedule_datum['activity_man_days'],
                            ];
                            $schedule_create = AuditVisitCalenderPlanMember::create($team_schedule);
                            \Log::info($schedule_create);
                        }
                    }
                }
            }
            DB::commit();
            $data = ['status' => 'success', 'data' => 'Saved'];
        } catch (\Exception $e) {
            DB::rollBack();
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        } catch (\Error $e) {
            DB::rollBack();
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        return $data;
    }

    public function updateTeamSchedule(Request $request): array
    {
        //return ['status' => 'error', 'data' => json_decode($request->team_schedules, true)];

        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        $team_schedules = json_decode($request->team_schedules, true);
        $team_schedules = $team_schedules['schedule'];
//        return ['status' => 'success', 'data' => $team_schedules];
        DB::beginTransaction();
        try {
            AuditVisitCalenderPlanMember::where('audit_plan_id', $request->audit_plan_id)->delete();
            $saveSchedule = $this->saveTeamSchedule($team_schedules, $request->audit_plan_id);
            if (isSuccessResponse($saveSchedule)) {
                $data = ['status' => 'success', 'data' => 'successfully saved'];
            } else {
                throw new \Exception($saveSchedule['data']);
            }
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
