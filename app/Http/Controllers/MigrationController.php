<?php

namespace App\Http\Controllers;

use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MigrationController extends Controller
{
    public function migrateAuditTeamSchedules(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->switchOffice($request->office_id);
            $teams = AuditVisitCalendarPlanTeam::where('team_schedules', '!=', null)->select('audit_plan_id', 'id', 'team_schedules')->get();
            foreach ($teams as $team_schedule) {
                $new_schedule = [];
                $schedules = json_decode($team_schedule['team_schedules'], true);
                $sequence = 0;
                foreach ($schedules as $schedule) {
                    $n = [];
                    $sequence++;
                    $new_schedule_arr = [];
                    $schedule_type = 'schedule';
                    $new_schedule_arr[$sequence] = [
                        "cost_center_id" => $schedule['cost_center_id'],
                        "cost_center_name_en" => $schedule['cost_center_name_en'],
                        "cost_center_name_bn" => $schedule['cost_center_name_bn'],
                        "team_member_start_date" => $schedule['team_member_start_date'],
                        "team_member_end_date" => $schedule['team_member_end_date'],
                        "activity_man_days" => $schedule['activity_man_days'],
                        "activity_details" => "",
                        "sequence_level" => $sequence,
                        "schedule_type" => $schedule_type,
                    ];
                    if ($schedule['activity_details'] != '') {
                        $sequence++;
                        $schedule_type = 'visit';
                        $new_schedule_arr[$sequence] = [
                            "cost_center_id" => 0,
                            "cost_center_name_en" => "",
                            "cost_center_name_bn" => "",
                            "team_member_start_date" => $schedule['team_member_start_date'],
                            "team_member_end_date" => $schedule['team_member_end_date'],
                            "activity_man_days" => 0,
                            "activity_details" => $schedule['activity_details'],
                            "sequence_level" => $sequence,
                            "schedule_type" => $schedule_type,
                        ];
                    }

                    $new_schedule[] = $new_schedule_arr;
                }

                foreach ($new_schedule as $ns) {
                    foreach ($ns as $sequence => $item) {
                        $n[$sequence] = $item;
                    }
                }
                $ts = AuditVisitCalendarPlanTeam::find($team_schedule['id']);
                $ts->team_schedules = json_encode($n, JSON_UNESCAPED_UNICODE);
                $ts->save();
                AuditVisitCalenderPlanMember::where('audit_plan_id', $ts->audit_plan_id)->delete();
                foreach ($n as $schedule_datum) {
                    $team_data = AuditVisitCalendarPlanTeam::where('audit_plan_id', $ts->audit_plan_id)->where('leader_designation_id', $ts->leader_designation_id)->first();
                    if (!$team_data) {
                        throw new \Exception('Team is not formed');
                    }
                    $team_member = json_decode($team_data->team_members, true);
                    foreach ($team_member as $key => $member_info) {
                        foreach ($member_info as $member) {
                            $team_schedule = [
                                'fiscal_year_id' => $team_data->fiscal_year_id,
                                'team_id' => $team_data->id,
                                'duration_id' => $team_data->duration_id,
                                'outcome_id' => $team_data->outcome_id,
                                'output_id' => $team_data->output_id,
                                'activity_id' => $team_data->activity_id,
                                'milestone_id' => $team_data->milestone_id,
                                'annual_plan_id' => $team_data->annual_plan_id,
                                'audit_plan_id' => $team_data->audit_plan_id,
                                'ministry_id' => $team_data->ministry_id,
                                'entity_id' => $team_data->entity_id,
                                'cost_center_id' => $schedule_datum['cost_center_id'],
                                'cost_center_name_en' => $schedule_datum['cost_center_name_en'],
                                'cost_center_name_bn' => $schedule_datum['cost_center_name_bn'],
                                'team_member_name_en' => $member['officer_name_en'],
                                'team_member_name_bn' => $member['officer_name_bn'],
                                'team_member_designation_id' => $member['designation_id'],
                                'team_member_officer_id' => $member['officer_id'],
                                'team_member_office_id' => $member['office_id'],
                                'team_member_designation_en' => $member['designation_en'],
                                'team_member_designation_bn' => $member['designation_bn'],
                                'team_member_role_en' => $member['team_member_role_en'],
                                'team_member_role_bn' => $member['team_member_role_bn'],
                                'team_member_start_date' => $schedule_datum['team_member_start_date'],
                                'team_member_end_date' => $schedule_datum['team_member_end_date'],
                                'comment' => $member['comment'] ?? '',
                                'mobile_no' => $member['officer_mobile'] ?? '',
                                'activity_location' => $schedule_datum['activity_details'],
                                'sequence_level' => $schedule_datum['sequence_level'],
                                'schedule_type' => $schedule_datum['schedule_type'],
                                'status' => 'pending',
                                'approve_status' => 'approved',
                            ];
                            AuditVisitCalenderPlanMember::create($team_schedule);
                        }
                    }
                }
                DB::commit();
            }
            return ['status' => 'success', 'data' => 'successfully saved'];
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
