<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AnnualPlanEntitie;
use App\Models\Apotti;
use App\Models\OpActivity;
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

//            return ['status' => 'success', 'data' => $schedules];


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

            $activity_id = $request->activity_id;
            $query = AnnualPlan::query();

            $query->when($activity_id, function ($q, $activity_id) {
                return $q->where('activity_id', $activity_id);
            });

            $annualPlanList = $query->with('ap_entities')
                                    ->with('activity:id,title_en,title_bn,activity_key')
                                    ->where('fiscal_year_id', $request->fiscal_year_id)
                                    ->orderBy('activity_id','ASC')->get();

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

            $annualPlanInfo = AnnualPlan::with('ap_milestones.milestone', 'ap_entities')->where('id', $request->annual_plan_id)->first();

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
        \DB::beginTransaction();
        try {
            $plan_data = [
                'schedule_id' => 0,
                'milestone_id' => 0,
                'activity_id' => $request->activity_id,
                'fiscal_year_id' => $request->fiscal_year_id,
                'op_audit_calendar_event_id' => $request->audit_calendar_event_id,
                'annual_plan_type' => $request->annual_plan_type,
                'office_type' => $request->office_type,
                'office_type_id' => $request->office_type_id,
                'office_type_en' => $request->office_type_en,
                'thematic_title' => $request->thematic_title,
                'budget' => filter_var(bnToen($request->budget), FILTER_SANITIZE_NUMBER_INT),
                'cost_center_total_budget' => filter_var(bnToen($request->cost_center_total_budget), FILTER_SANITIZE_NUMBER_INT),
                'total_unit_no' => $request->total_unit_no,
                'nominated_office_counts' => $request->total_selected_unit_no,
                'subject_matter' => $request->subject_matter,
                'sub_subject_matter' => $request->sub_subject_matter,
                'vumika' => $request->vumika,
                'audit_objective' => $request->audit_objective,
                'audit_approach' => $request->audit_approach,
                'nominated_man_powers' => $request->nominated_man_powers,
                'nominated_man_power_counts' => $request->nominated_man_power_counts,
                'comment' => empty($request->comment) ? null : $request->comment,
            ];

            $plan = AnnualPlan::create($plan_data);
            foreach ($request->milestone_list as $milestone) {
                $ap_milestone = new ApMilestone();
                $ap_milestone->fiscal_year_id = $milestone['fiscal_year_id'];
                $ap_milestone->annual_plan_id = $plan->id;
                $ap_milestone->activity_id = $milestone['activity_id'];
                $ap_milestone->milestone_id = $milestone['milestone_id'];
                $ap_milestone->milestone_target_date = $milestone['milestone_target_date'];
                $ap_milestone->start_date = $milestone['start_date'];
                $ap_milestone->end_date = $milestone['end_date'];
                $ap_milestone->save();
            }

            foreach ($request->entity_list as $key => $entity) {
                if ($key != 'undefined') {
                    $ap_entity = new AnnualPlanEntitie();
                    $ap_entity->annual_plan_id = $plan->id;
//                    $ap_entity->layer_id = $entity['layer_id'];
                    $ap_entity->layer_id = 0;
                    $ap_entity->ministry_id = $entity['ministry_id'];
                    $ap_entity->ministry_name_bn = $entity['ministry_name_bn'];
                    $ap_entity->ministry_name_en = $entity['ministry_name_en'];
                    $ap_entity->entity_id = $entity['entity_id'];
                    $ap_entity->entity_name_bn = $entity['entity_bn'];
                    $ap_entity->entity_name_en = $entity['entity_en'];
                    $ap_entity->entity_total_unit = $entity['entity_total_unit'];
                    $ap_entity->nominated_offices =  isset($entity['nominated_offices']) ? json_encode($entity['nominated_offices']) : json_encode([]);
                    $ap_entity->created_at = date('Y-m-d H:i:s');
                    $ap_entity->save();
                }
            }
            \DB::commit();
            $data = ['status' => 'success', 'data' => 'Annual Plan Save Successfully'];
        } catch (\Error $exception) {
            \DB::rollback();
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        } catch (\Exception $exception) {
            \DB::rollback();
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
        \DB::beginTransaction();
        try {
            $plan_data = [
                'schedule_id' => 0,
                'milestone_id' => 0,
                'activity_id' => $request->activity_id,
                'fiscal_year_id' => $request->fiscal_year_id,
                'op_audit_calendar_event_id' => $request->audit_calendar_event_id,
                'office_type' => $request->office_type,
                'office_type_id' => $request->office_type_id,
                'office_type_en' => $request->office_type_en,
                'annual_plan_type' => $request->annual_plan_type,
                'thematic_title' => $request->thematic_title,
                'budget' => filter_var(bnToen($request->budget), FILTER_SANITIZE_NUMBER_INT),
                'cost_center_total_budget' => filter_var(bnToen($request->cost_center_total_budget), FILTER_SANITIZE_NUMBER_INT),
                'total_unit_no' => $request->total_unit_no,
                'nominated_office_counts' => $request->total_selected_unit_no,
                'subject_matter' => $request->subject_matter,
                'sub_subject_matter' => $request->sub_subject_matter,
                'vumika' => $request->vumika,
                'audit_objective' => $request->audit_objective,
                'audit_approach' => $request->audit_approach,
                'nominated_man_powers' => $request->nominated_man_powers,
                'nominated_man_power_counts' => $request->nominated_man_power_counts,
                'comment' => empty($request->comment) ? null : $request->comment,
            ];

            AnnualPlan::where('id', $request->id)->update($plan_data);
//            $assigned_info = OpYearlyAuditCalendarResponsible::select('assigned_staffs','assigned_budget')->where('activity_id',$request->activity_id)
//                ->where('office_id',$cdesk->office_id)->first();
//
//            $assigned_staffs = $assigned_info->assigned_staffs + $request->nominated_man_power_counts;
//            $assigned_budget = $assigned_info->assigned_budget + $request->budget;
//
//            OpYearlyAuditCalendarResponsible::where('activity_id',$request->activity_id)
//                ->where('office_id',$cdesk->office_id)->update(['assigned_staffs'=> $assigned_staffs,'assigned_budget' => $assigned_budget]);

            ApMilestone::where('annual_plan_id', $request->id)->delete();

            foreach ($request->milestone_list as $milestone) {
                $ap_milestone = new ApMilestone();
                $ap_milestone->fiscal_year_id = $milestone['fiscal_year_id'];
                $ap_milestone->annual_plan_id = $request->id;
                $ap_milestone->activity_id = $milestone['activity_id'];
                $ap_milestone->milestone_id = $milestone['milestone_id'];
                $ap_milestone->milestone_target_date = $milestone['milestone_target_date'];
                $ap_milestone->start_date = $milestone['start_date'];
                $ap_milestone->end_date = $milestone['end_date'];
                $ap_milestone->save();
            }

            AnnualPlanEntitie::where('annual_plan_id', $request->id)->delete();

            foreach ($request->entity_list as $key => $entity) {
                if ($key != 'undefined') {
                    $ap_entity = new AnnualPlanEntitie();
                    $ap_entity->annual_plan_id = $request->id;
//                    $ap_entity->layer_id = $entity['layer_id'];
                    $ap_entity->layer_id = 0;
                    $ap_entity->ministry_id = $entity['ministry_id'];
                    $ap_entity->ministry_name_bn = $entity['ministry_name_bn'];
                    $ap_entity->ministry_name_en = $entity['ministry_name_en'];
                    $ap_entity->entity_id = $entity['entity_id'];
                    $ap_entity->entity_name_bn = $entity['entity_bn'];
                    $ap_entity->entity_name_en = $entity['entity_en'];
                    $ap_entity->entity_total_unit = $entity['entity_total_unit'];
                    $ap_entity->nominated_offices = isset($entity['nominated_offices']) ? json_encode($entity['nominated_offices']) : json_encode([]);
                    $ap_entity->save();
                }
            }
            \DB::commit();
            $data = ['status' => 'success', 'data' => 'Successfully Plan Updated!'];
        } catch (\Error $exception) {
            \DB::rollback();
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        } catch (\Exception $exception) {
            \DB::rollback();
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

            $plan_datas = AnnualPlan::with(['ap_entities'])->where('fiscal_year_id', $request->fiscal_year_id)->with('activity.milestones.milestone_calendar')->get()->toArray();
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

                $ministriesBn = [];
                $ministriesEn = [];
                foreach ($plan_data['ap_entities'] as $ap_entities) {
                    $ministryBn = $ap_entities['ministry_name_bn'];
                    $ministriesBn[] = $ministryBn;
                    $ministryEn = $ap_entities['ministry_name_en'];
                    $ministriesEn[] = $ministryEn;
                }

                $ministries = [
                    'ministry_name_en' => implode(' , ', array_unique($ministriesEn)),
                    'ministry_name_bn' => implode(' , ', array_unique($ministriesBn)),
                ];

                $all_ministries[$plan_data['id']] = $ministries;
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

    public function deleteAnnualPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            AnnualPlan::find($request->annual_plan_id)->delete();
            AnnualPlanEntitie::where('annual_plan_id',$request->annual_plan_id)->delete();
            ApMilestone::where('annual_plan_id',$request->annual_plan_id)->delete();

            $data = ['status' => 'success', 'data' => 'Annual Plan Delete Successfully'];

        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $data;

    }
}
