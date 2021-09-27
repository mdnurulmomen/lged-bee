<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityAuditPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApOrganizationYearlyPlanResponsibleParty;
use App\Models\AuditTemplate;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\OpActivity;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApEntityAuditPlanRevisedService
{
    use GenericData;

    public function allEntityAuditPlanLists(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            if ($request->per_page && $request->page && !$request->all) {
                $all_entities = AnnualPlan::with('audit_plans')->where('fiscal_year_id', $fiscal_year_id)->paginate($request->per_page);
            } else {
                $all_entities = AnnualPlan::with('audit_plans')->where('fiscal_year_id', $fiscal_year_id)->get();
            }
            return ['status' => 'success', 'data' => $all_entities];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function createNewAuditPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $annual_plan = AnnualPlan::where('id', $request->annual_plan_id)->with('fiscal_year')->first()->toArray();
            $activity = OpActivity::where('id', $request->activity_id)->first()->toArray();
            $audit_template = AuditTemplate::where('template_type', $activity['activity_type'])->where('lang', 'bn')->first()->toArray();

            $data['annual_plan'] = $annual_plan;
            $data['plan_description'] = $audit_template['content'];
            $data['audit_type'] = $activity['activity_type'];
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function editAuditPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $audit_template = ApEntityIndividualAuditPlan::find($request->audit_plan_id)->toArray();
            return ['status' => 'success', 'data' => $audit_template];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function update(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $this->switchOffice($cdesk->office_id);

        try {
            $annual_plan_data = AnnualPlan::where('id', $request->annual_plan_id)->select('schedule_id', 'milestone_id', 'fiscal_year_id')->first();
            $draft_plan_data = [
                'activity_id' => $request->activity_id,
                'schedule_id' => $annual_plan_data->schedule_id,
                'milestone_id' => $annual_plan_data->milestone_id,
                'fiscal_year_id' => $annual_plan_data->fiscal_year_id,
                'annual_plan_id' => $request->annual_plan_id,
                'plan_description' => $request->plan_description,
                'draft_office_id' => $cdesk->office_id,
                'draft_unit_id' => $cdesk->office_unit_id,
                'draft_unit_name_en' => $cdesk->office_unit_en,
                'draft_unit_name_bn' => $cdesk->office_unit_bn,
                'draft_designation_id' => $cdesk->designation_id,
                'draft_designation_name_en' => $cdesk->designation_en,
                'draft_designation_name_bn' => $cdesk->designation_bn,
                'draft_officer_id' => $cdesk->officer_id,
                'draft_officer_name_en' => $cdesk->officer_en,
                'draft_officer_name_bn' => $cdesk->officer_bn,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
                'device_type' => '',
                'device_id' => '',
            ];


            if ($request->has('audit_plan_id') && $request->audit_plan_id > 0) {
                $draft_plan = ApEntityIndividualAuditPlan::where('id', $request->audit_plan_id)
                    ->where('activity_id', $request->activity_id)
                    ->where('annual_plan_id', $request->annual_plan_id)
                    ->update($draft_plan_data);
            } else {
                $draft_plan = ApEntityIndividualAuditPlan::create($draft_plan_data);
            }

            $data = ['status' => 'success', 'data' => $draft_plan];
        } catch (\Exception $e) {
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();

        return $data;
    }

    public function showEntityAuditPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $this->switchOffice($cdesk->office_id);

        try {
            $plan = ApEntityAuditPlan::where('party_id', $request->party_id)->where('ap_organization_yearly_plan_rp_id', $request->yearly_plan_rp_id)->first();
            if ($plan) {
                $content = $plan['plan_description'];
                unset($plan['plan_description']);
                $plan = ['content' => $content, 'plan_details' => $plan, 'is_draft' => true];
                $data = ['status' => 'success', 'data' => $plan];
            } else {
                $yearly_plan_rp = ApOrganizationYearlyPlanResponsibleParty::where('id', $request->yearly_plan_rp_id)->with('activity')->first();
                if (!$yearly_plan_rp) {
                    throw new \Exception('Yearly Plan Of RP Unit Not Found!');
                }
                $activity_type = $yearly_plan_rp->activity->activity_type;
                if ($activity_type) {
                    $template = AuditTemplate::select('content')->where('lang', $request->lang)->where('template_type', $activity_type)->where('status', 1)->first()->toArray();
                    $template['is_draft'] = false;
                    $template['plan_details'] = new \stdClass();
                    $data = ['status' => 'success', 'data' => $template];
                } else {
                    throw new \Exception('No template found');
                }
            }
        } catch (\Exception $e) {
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $data;

    }

    public function storeAuditTeam(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $this->switchOffice($cdesk->office_id);

        $annualPlan = AnnualPlan::find($request->annual_plan_id);

        $teams = json_decode($request->teams, true);
        $teams = $teams['teams'];
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

            $data = ['status' => 'success', 'data' => 'save data successful'];
        } catch (\Exception $e) {
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();

        return $data;
    }

    public function storeTeamSchedule(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $this->switchOffice($cdesk->office_id);
        $team_schedules = json_decode($request->team_schedules, true);
        $team_schedules = $team_schedules['schedule'];
        DB::beginTransaction();
        try {
            foreach ($team_schedules as $designation_id => $schedule_data) {
                foreach ($schedule_data as $schedule_datum) {
                    $team_data = AuditVisitCalendarPlanTeam::where('audit_plan_id', $request->audit_plan_id)->where('leader_designation_id', $designation_id)->first();
                    $team_data->team_schedules = json_encode_unicode($schedule_data);
                    $team_data->save();
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
                                'cost_center_name_bn' => $schedule_datum['cost_center_name_en'],
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
                                'comment' => isset($member['comment']) ?? '',
                                'mobile_no' => isset($member['officer_mobile']) ?: '',
                                'team_member_activity' => array_key_exists('team_member_activity', $schedule_datum) ? $schedule_datum['team_member_activity'] : '',
                                'team_member_activity_description' => array_key_exists('team_member_activity_description', $schedule_datum) ? $schedule_datum['team_member_activity_description'] : '',
                                'activity_location' => array_key_exists('activity_location', $schedule_datum) ? $schedule_datum['activity_location'] : '',
                                'approve_status' => 'approved',
                            ];
                            AuditVisitCalenderPlanMember::create($team_schedule);
                        }
                    }
                }
            }
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

    public function getAuditPlanWiseTeam(Request $request)
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $teams = AuditVisitCalendarPlanTeam::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('activity_id', $request->activity_id)
                ->where('audit_plan_id', $request->audit_plan_id)
                ->where('annual_plan_id', $request->annual_plan_id)
                ->get()
                ->toArray();

            $data = ['status' => 'success', 'data' => $teams];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $data;
    }

    public function getSubTeam(Request $request)
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $data = AuditVisitCalendarPlanTeam::where('team_parent_id', $request->team_id)->get()->toArray();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
