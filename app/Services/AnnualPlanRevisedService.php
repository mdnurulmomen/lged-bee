<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\OpOrganizationYearlyAuditCalendarEvent;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Models\XFiscalYear;
use App\Models\XResponsibleOffice;
use App\Models\ApMilestone;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class AnnualPlanRevisedService
{
    use GenericData;

    public function allAnnualPlans(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;
        $cdesk = json_decode($request->cdesk, false);

        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $schedules = OpOrganizationYearlyAuditCalendarEventSchedule::where('fiscal_year_id', $fiscal_year_id)
                ->where('activity_responsible_id', $cdesk->office_id)
                ->select('id AS schedule_id', 'op_audit_calendar_event_id', 'fiscal_year_id', 'activity_id', 'activity_type', 'activity_title_en', 'activity_title_bn', 'activity_responsible_id AS office_id', 'activity_milestone_id', 'op_yearly_audit_calendar_activity_id', 'op_yearly_audit_calendar_id', 'milestone_title_en', 'milestone_title_bn', 'milestone_target')
                ->with(['annual_plan', 'op_organization_yearly_audit_calendar_event'])
                ->get()
                ->groupBy('activity_id')
                ->toArray();

            foreach ($schedules as $key => &$milestone) {
                foreach ($milestone as &$ms) {
                    $assigned_budget = 0;
                    $assigned_staff = 0;
                    foreach ($ms['annual_plan'] as $annual_plan) {
                        $assigned_budget = $assigned_budget + (int)$annual_plan['budget'];
                        $assigned_staff = $assigned_staff + (int)$annual_plan['nominated_man_power_counts'];
                    }
                    $ms['assigned_budget'] = $assigned_budget;
                    $ms['assigned_staff'] = $assigned_staff;
                }
            }

            $data = ['status' => 'success', 'data' => $schedules];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $data;

    }

    public function showAnnualPlans(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $annualPlanList = AnnualPlan::where('fiscal_year_id', $request->fiscal_year_id)->get();
            $data = ['status' => 'success', 'data' => $annualPlanList];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $data;
    }

    public function showAnnualPlanEntities(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $annualPlanList = AnnualPlan::where('fiscal_year_id', $request->fiscal_year_id)
//                ->where('milestone_id', $request->milestone_id)
                ->get();

            $approval_status = OpOrganizationYearlyAuditCalendarEvent::select('approval_status')
                ->where('office_id', $cdesk->office_id)
                ->first()->approval_status;
            $op_audit_calendar_event_id = OpOrganizationYearlyAuditCalendarEventSchedule::select('op_audit_calendar_event_id')->where('fiscal_year_id', $request->fiscal_year_id)->first()->op_audit_calendar_event_id;

            $annualPlan['annual_plan_list'] = $annualPlanList;
            $annualPlan['approval_status'] = $approval_status;
            $annualPlan['op_audit_calendar_event_id'] = $op_audit_calendar_event_id;

            $data = ['status' => 'success', 'data' => $annualPlan];

        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $data;

    }

    public function getAnnualPlanInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $annualPlanInfo = AnnualPlan::with('ap_milestones.milestone')->where('id', $request->annual_plan_id)->first();

            $data = ['status' => 'success', 'data' => $annualPlanInfo];

        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $data;

    }

    public function storeAnnualPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ministry = json_decode($request->ministry_info);
            $controlling_office = json_decode($request->controlling_office);
            $parent_office = json_decode($request->parent_office);

//           $schedule_id =  OpOrganizationYearlyAuditCalendarEventSchedule::select('id')->where('activity_id',$request->activity_id)->where('activity_milestone_id',$request->milestone_id)->first()->id;

            $plan_data = [
                'schedule_id' => 0,
                'milestone_id' => 0,
                'activity_id' => $request->activity_id,
                'fiscal_year_id' => $request->fiscal_year_id,
                'op_audit_calendar_event_id' => $request->audit_calendar_event_id,

                'ministry_name_en' => $ministry->ministry_name_en,
                'ministry_name_bn' => $ministry->ministry_name_bn,
                'ministry_id' => $ministry->ministry_id,

                'controlling_office_en' => $controlling_office->controlling_office_name_en,
                'controlling_office_bn' => $controlling_office->controlling_office_name_bn,
                'controlling_office_id' => $controlling_office->controlling_office_id,

                'parent_office_name_en' => $parent_office->parent_office_name_en,
                'parent_office_name_bn' => $parent_office->parent_office_name_bn,
                'parent_office_id' => $parent_office->parent_office_id,
                'office_type' => $request->office_type,
                'annual_plan_type' => $request->annual_plan_type,

                'budget' => filter_var(bnToen($request->budget), FILTER_SANITIZE_NUMBER_INT),
                'cost_center_total_budget' => filter_var(bnToen($request->cost_center_total_budget), FILTER_SANITIZE_NUMBER_INT),
                'total_unit_no' => $request->total_unit_no,
                'nominated_offices' => $request->nominated_offices,
                'nominated_office_counts' => count(json_decode($request->nominated_offices, true)),
                'subject_matter' => $request->subject_matter,
                'nominated_man_powers' => $request->nominated_man_powers,
                'nominated_man_power_counts' => $request->nominated_man_power_counts,
                'comment' => empty($request->comment) ? null : $request->comment,
            ];

            $plan = AnnualPlan::create($plan_data);

            foreach ($request->milestone_list as $milestone){
               $ap_milestone =  New ApMilestone();
               $ap_milestone->fiscal_year_id = $milestone['fiscal_year_id'];
               $ap_milestone->annual_plan_id = $plan->id;
               $ap_milestone->activity_id = $milestone['activity_id'];
               $ap_milestone->milestone_id = $milestone['milestone_id'];
               $ap_milestone->milestone_target_date = date('Y-m-d',strtotime($milestone['milestone_target_date']));
               $ap_milestone->start_date = date('Y-m-d',strtotime($milestone['start_date']));
               $ap_milestone->end_date = date('Y-m-d',strtotime($milestone['end_date']));
               $ap_milestone->save();
            }

            $data = ['status' => 'success', 'data' => $plan];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $data;
    }

    public function updateAnnualPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ministry = json_decode($request->ministry_info);
            $controlling_office = json_decode($request->controlling_office);
            $parent_office = json_decode($request->parent_office);

//            $schedule_id =  OpOrganizationYearlyAuditCalendarEventSchedule::select('id')->where('activity_id',$request->activity_id)->where('activity_milestone_id',$request->milestone_id)->first()->id;

            $plan_data = [
                'schedule_id' => 0,
                'milestone_id' => 0,
                'activity_id' => $request->activity_id,
                'fiscal_year_id' => $request->fiscal_year_id,
                'op_audit_calendar_event_id' => $request->audit_calendar_event_id,

                'ministry_name_en' => $ministry->ministry_name_en,
                'ministry_name_bn' => $ministry->ministry_name_bn,
                'ministry_id' => $ministry->ministry_id,

                'controlling_office_en' => $controlling_office->controlling_office_name_en,
                'controlling_office_bn' => $controlling_office->controlling_office_name_bn,
                'controlling_office_id' => $controlling_office->controlling_office_id,

                'parent_office_name_en' => $parent_office->parent_office_name_en,
                'parent_office_name_bn' => $parent_office->parent_office_name_bn,
                'parent_office_id' => $parent_office->parent_office_id,
                'office_type' => $request->office_type,
                'annual_plan_type' => $request->annual_plan_type,

                'budget' => filter_var(bnToen($request->budget), FILTER_SANITIZE_NUMBER_INT),
                'cost_center_total_budget' => filter_var(bnToen($request->cost_center_total_budget), FILTER_SANITIZE_NUMBER_INT),
                'total_unit_no' => $request->total_unit_no,
                'nominated_offices' => $request->nominated_offices,
                'nominated_office_counts' => count(json_decode($request->nominated_offices, true)),
                'subject_matter' => $request->subject_matter,
                'nominated_man_powers' => $request->nominated_man_powers,
                'nominated_man_power_counts' => $request->nominated_man_power_counts,
                'comment' => empty($request->comment) ? null : $request->comment,
            ];
            $plan = AnnualPlan::where('id',$request->id)->update($plan_data);

            ApMilestone::where('annual_plan_id',$request->id)->delete();

            foreach ($request->milestone_list as $milestone){
               $ap_milestone =  New ApMilestone();
               $ap_milestone->fiscal_year_id = $milestone['fiscal_year_id'];
               $ap_milestone->annual_plan_id = $request->id;
               $ap_milestone->activity_id = $milestone['activity_id'];
               $ap_milestone->milestone_id = $milestone['milestone_id'];
               $ap_milestone->milestone_target_date = date('Y-m-d',strtotime($milestone['milestone_target_date']));
               $ap_milestone->start_date = date('Y-m-d',strtotime($milestone['start_date']));
               $ap_milestone->end_date = date('Y-m-d',strtotime($milestone['end_date']));
               $ap_milestone->save();
            }

            $data = ['status' => 'success', 'data' => 'Successfully Plan Updated!'];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $data;
    }

    public function exportAnnualPlanBook(Request $request): array
    {
        $office_id = $request->office_id;

        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $directorate = XResponsibleOffice::where('office_id', $office_id)->select('office_name_en', 'office_name_bn', 'short_name_en', 'short_name_bn')->first()->toArray();

            $plan_datas = AnnualPlan::where('fiscal_year_id', $request->fiscal_year_id)->with('activity.milestones.milestone_calendar')->get()->toArray();
            $fiscal_year = XFiscalYear::findOrFail($request->fiscal_year_id, ['start', 'end', 'description'])->toArray();
            $plan_data_final = [];
            $all_ministries = [];
            $annual_plan = [];
            foreach ($plan_datas as $plan_data) {
                $activity = $plan_data['activity'];
                unset($plan_data['activity']);
                $annual_plan[$plan_data['id']] = $plan_data;
                foreach ($annual_plan as $plan) {
                    if ($activity['id'] == $plan['activity_id']) {
                        $annual_plan[$plan_data['id']] = $plan_data;
                    } else {
                        $annual_plan = [];
                    }
                }
                $ministries = [
                    'ministry_name_en' => $plan_data['ministry_name_en'],
                    'ministry_name_bn' => $plan_data['ministry_name_bn'],
                ];

                $all_ministries[$plan_data['ministry_id']] = $ministries;
                $plan_data_final[$activity['id']] = $activity + ['ministries' => $ministries] + ['annual_plans' => $annual_plan];
            }
            $pdf_data = [
                'office_info' => $directorate,
                'plans' => $plan_data_final,
                'all_ministries' => $all_ministries,
                'fiscal_year' => $fiscal_year,
            ];


            $data = ['status' => 'success', 'data' => $pdf_data];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $data;
    }

    public function showNominatedOffices(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $nominated_offices = AnnualPlan::find($request->annual_plan_id, ['nominated_offices', 'nominated_office_counts']);
            $data = ['status' => 'success', 'data' => $nominated_offices];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $data;
    }

    public function submitPlanToOCAG(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $submission_datas = OpOrganizationYearlyAuditCalendarEventSchedule::where('fiscal_year_id', $fiscal_year_id)
                ->where('activity_responsible_id', $cdesk->office_id)
                ->select('id AS schedule_id', 'fiscal_year_id', 'activity_id', 'activity_type', 'activity_title_en', 'activity_title_bn', 'activity_responsible_id AS office_id', 'activity_milestone_id', 'op_yearly_audit_calendar_activity_id', 'op_yearly_audit_calendar_id', 'milestone_title_en', 'milestone_title_bn', 'milestone_target')
                ->with(['annual_plan'])
                ->get()
                ->groupBy('activity_id')
                ->toArray();

            $s_data = [];
            foreach ($submission_datas as $key => &$milestone) {
                $assigned_budget = 0;
                $assigned_staff = 0;
                foreach ($milestone as &$ms) {
                    foreach ($ms['annual_plan'] as $annual_plan) {
                        $assigned_budget = $assigned_budget + (int)$annual_plan['budget'];
                        $assigned_staff = $assigned_staff + (int)$annual_plan['nominated_man_power_counts'];
                    }
                    $s_data[$key]['assigned_budget'] = $assigned_budget;
                    $s_data[$key]['assigned_staffs'] = $assigned_staff;
                }
            }

            foreach ($s_data as $activity_id => $s_datum) {
                OpYearlyAuditCalendarResponsible::where('activity_id', $activity_id)->where('office_id', $cdesk->office_id)->update($s_datum);
            }

            $this->emptyOfficeDBConnection();
            return ['status' => 'success', 'data' => $s_data];
        } catch (\Exception $e) {
            return ['status' => 'error', 'data' => $e->getMessage()];
        }
    }
}
