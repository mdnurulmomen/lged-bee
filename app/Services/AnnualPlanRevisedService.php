<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\OpActivity;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Models\XFiscalYear;
use App\Models\XResponsibleOffice;
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
                ->with(['annual_plan'])
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
                ->where('milestone_id', $request->milestone_id)
                ->get();
            $data = ['status' => 'success', 'data' => $annualPlanList];
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

            $nominated_offices = $request->nominated_offices;
            $ministry_info = json_decode($request->ministry_info, true);
            $controlling_office = json_decode($request->controlling_office, true);
            $parent_office = json_decode($request->parent_office, true);
            $entity_ids = (array_keys(json_decode($nominated_offices, true)));

            //dd($parent_office);

            foreach ($ministry_info as $ministry) {
                $is_entity_ministry = !empty(array_intersect($entity_ids, $ministry['entity_ids']));
                if ($is_entity_ministry) {
                    foreach ($controlling_office as $controller) {
                        $is_entity_controller = !empty(array_intersect($entity_ids, $controller['entity_ids']));
                        if ($is_entity_controller) {

                            foreach ($parent_office as $parent) {
                                $is_entity_parent_controller = !empty(array_intersect($entity_ids, $parent['entity_ids']));
                                if ($is_entity_parent_controller) {
                                    $plan_data = [
                                        'schedule_id' => $request->schedule_id,
                                        'milestone_id' => $request->milestone_id,
                                        'activity_id' => $request->activity_id,
                                        'fiscal_year_id' => $request->fiscal_year_id,
                                        'op_audit_calendar_event_id' => $request->audit_calendar_event_id,
                                        'ministry_name_en' => $ministry['ministry_name_en'],
                                        'ministry_name_bn' => $ministry['ministry_name_bn'],
                                        'ministry_id' => $ministry['ministry_id'],
                                        'controlling_office_en' => $controller['controlling_office_name_en'],
                                        'controlling_office_bn' => $controller['controlling_office_name_bn'],
                                        'controlling_office_id' => $controller['controlling_office_id'],

                                        'parent_office_name_en' => $parent['parent_office_name_en'],
                                        'parent_office_name_bn' => $parent['parent_office_name_bn'],
                                        'parent_office_id' => $parent['parent_office_id'],

                                        'office_type' => $controller['office_type'],
                                        'budget' => filter_var(bnToen($request->budget), FILTER_SANITIZE_NUMBER_INT),
                                        'total_unit_no' => $request->total_unit_no,
                                        'nominated_offices' => $request->nominated_offices,
                                        'nominated_office_counts' => count(json_decode($request->nominated_offices, true)),
                                        'subject_matter' => $request->subject_matter,
                                        'nominated_man_powers' => $request->nominated_man_powers,
                                        'nominated_man_power_counts' => $request->nominated_man_power_counts,
                                        'comment' => $request->comment,
                                    ];
                                }

                                $plan = AnnualPlan::updateOrcreate([
                                    'ministry_id' => $ministry['ministry_id'],
                                    'parent_office_id' => $parent['parent_office_id'],
                                ], $plan_data);
                            }

                        }
                    }
                }
            }
            $data = ['status' => 'success', 'data' => 'Successfully Plan Created!'];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $data;
    }

    public function exportAnnualPlanBook(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $directorate = XResponsibleOffice::where('office_id', $cdesk->office_id)->select('office_name_en', 'office_name_bn', 'short_name_en', 'short_name_bn')->first()->toArray();

//            $plan_datas = OpOrganizationYearlyAuditCalendarEventSchedule::where('fiscal_year_id', $request->fiscal_year_id)->with('milestones.annual_plans')->get()->toArray();
//            $plan_datas = AnnualPlan::where('fiscal_year_id', $request->fiscal_year_id)->with('yearly_audit_calendar_event_schedule.activity.milestones.milestone_calendar')->get()->toArray();

            $plan_datas = AnnualPlan::where('fiscal_year_id', $request->fiscal_year_id)->with('activity.milestones.milestone_calendar')->get()->toArray();
            $fiscal_year = XFiscalYear::findOrFail($request->fiscal_year_id, ['start', 'end', 'description'])->toArray();
            $plan_data_final = [];
            $ministries = [];
            $all_ministries = [];
            $activity = [];

            foreach ($plan_datas as $plan_data) {
                $ministries[$plan_data['ministry_id']] = [
                    'ministry_name_en' => $plan_data['ministry_name_en'],
                    'ministry_name_bn' => $plan_data['ministry_name_bn'],
                ];

                $activity = $plan_data['activity'];
                unset($plan_data['activity']);

                $all_ministries = $ministries;
                $plan_data_final[$plan_data['id']] = $activity + ['ministries' => $ministries] + ['annual_plans' => $plan_data];
            }

//            foreach ($plan_datas as $plan_data) {
//                foreach ($plan_data['milestones'] as $plan_datum) {
//                    if (!empty($plan_datum['annual_plans'])) {
//                        foreach ($plan_datum['annual_plans'] as $plan) {
//                            $ministries[$plan['ministry_id']] = [
//                                'ministry_name_en' => $plan['ministry_name_en'],
//                                'ministry_name_bn' => $plan['ministry_name_bn'],
//                            ];
//                            if ($plan['milestone_id'] == $plan_datum['id']) {
//                                $annual_plans[$plan['id']] = $plan;
//                            }
//                        }
//                        $all_ministries = $ministries;
//                        $plan_data_final[$plan_datum['id']] = $plan_data + ['ministries' => $ministries] + ['annual_plans' => $annual_plans];
//                    }
//                }
//            }

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
