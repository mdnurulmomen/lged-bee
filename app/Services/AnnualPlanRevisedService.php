<?php

namespace App\Services;

use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApPsrSubjectMatter;
use App\Models\ApPsrAduitObject;
use App\Models\ApPsrLineOfEnquire;
use App\Models\AnnualPlan;
use App\Models\AnnualPlanEntitie;
use App\Models\AnnualPlanMain;
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
use DB;

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
                ->select('staff_assigne', 'no_of_items', 'id AS schedule_id', 'op_audit_calendar_event_id', 'fiscal_year_id', 'activity_id', 'activity_type', 'activity_title_en', 'activity_title_bn', 'activity_responsible_id AS office_id', 'activity_milestone_id', 'op_yearly_audit_calendar_activity_id', 'op_yearly_audit_calendar_id', 'milestone_title_en', 'milestone_title_bn', 'milestone_target')
                ->with(['annual_plan', 'op_organization_yearly_audit_calendar_event'])
                ->get()
                ->groupBy('activity_id')
                ->toArray();

            foreach ($schedules as $key => &$milestone) {

                $no_of_items = array_column($milestone, 'no_of_items');
                $total_no_of_items =  array_sum($no_of_items);

                $assigned_staff = array_column($milestone, 'staff_assigne');
                $total_assigned_staff =  array_sum($assigned_staff);

                foreach ($milestone as &$ms) {
                    $assigned_budget = 0;
                    $assigned_staff = 0;

                    foreach ($ms['annual_plan'] as $annual_plan) {
                        $assigned_budget = $assigned_budget + (int)$annual_plan['budget'];
                        $assigned_staff = $assigned_staff + (int)$annual_plan['nominated_man_power_counts'];
                    }


                    $ms['assigned_budget'] = $assigned_budget;
                    $ms['assigned_staff'] = $assigned_staff;
                    $ms['total_no_of_items'] = $total_no_of_items;
                    $ms['total_assigned_staff'] = $total_assigned_staff;
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
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $activity_id = $request->activity_id;
            $activity_type = $request->activity_type;
            $query = AnnualPlanMain::query();

            $annualPlanList = $query->with('annual_plan_items.ap_entities')
                ->with('annual_plan_items.activity:id,title_en,title_bn,activity_key')
                ->where('fiscal_year_id', $request->fiscal_year_id)
                ->where('activity_type', $activity_type)
                ->with('annual_plan_items', function ($q) use ($activity_id) {
                    return $q->where('activity_id', $activity_id);
                })->first();

            $op_audit_calendar_event_id = OpOrganizationYearlyAuditCalendarEventSchedule::select('op_audit_calendar_event_id')->where('fiscal_year_id', $request->fiscal_year_id)->first()->op_audit_calendar_event_id;

            $annualPlan['annual_plan_list'] = $annualPlanList;
            $annualPlan['approval_status'] = '';
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
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
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
    public function getAnnualPlanSubjectMatterInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $annualPlanSubjectMatter = ApPsrSubjectMatter::where('annual_plan_id', $request->annual_plan_id)->where('parent_id', 0)->first();
            $annualPlanSubSubjectMatter = ApPsrSubjectMatter::where('parent_id', $annualPlanSubjectMatter->id)->get();

            $annualPlanAduitObjective = ApPsrAduitObject::where('annual_plan_id', $request->annual_plan_id)->where('parent_id', 0)->first();
            $annualPlanSubAduitObjective = ApPsrAduitObject::where('parent_id', $annualPlanAduitObjective->id)
                ->with('line_of_enquiries')
                ->get();

            $annualPlanSubjectMatterInfo['main_topic'] = $annualPlanSubjectMatter;
            $annualPlanSubjectMatterInfo['sub_topic'] = $annualPlanSubSubjectMatter;
            $annualPlanSubjectMatterInfo['aduit_object'] = $annualPlanAduitObjective;
            $annualPlanSubjectMatterInfo['sub_object'] = $annualPlanSubAduitObjective;

            $data = ['status' => 'success', 'data' => $annualPlanSubjectMatterInfo];
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

            //            return ['status' => 'error', 'data' => $request->all()];
            $plan_data = [
                'schedule_id' => 0,
                'milestone_id' => 0,
                'activity_id' => $request->activity_id,
                'activity_type' => $request->activity_type ?: 'compliance',
                'fiscal_year_id' => $request->fiscal_year_id,
                'op_audit_calendar_event_id' => $request->audit_calendar_event_id,
                'annual_plan_type' => $request->annual_plan_type,
                'office_type' => $request->office_type,
                'office_type_id' => $request->office_type_id,
                'office_type_en' => $request->office_type_en,
                'thematic_title' => $request->thematic_title,
                'budget' => filter_var(bnToen($request->budget), FILTER_SANITIZE_NUMBER_INT),
                'cost_center_total_budget' => filter_var(bnToen($request->cost_center_total_budget), FILTER_SANITIZE_NUMBER_INT),
                'total_expenditure' => filter_var(bnToen($request->total_expenditure), FILTER_SANITIZE_NUMBER_INT),
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
                'project_id' => empty($request->project_id) ? null : $request->project_id,
                'project_name_bn' => empty($request->project_name_bn) ? null : $request->project_name_bn,
                'project_name_en' => empty($request->project_name_en) ? null : $request->project_name_en,
                'created_by' => $cdesk->officer_id,
            ];

            if ($request->annual_plan_main_id) {
                $plan_data['annual_plan_main_id'] = $request->annual_plan_main_id;
            } else {
                $main_plan = new AnnualPlanMain();
                $main_plan->fiscal_year_id = $request->fiscal_year_id;
                $main_plan->op_audit_calendar_event_id = $request->audit_calendar_event_id;
                $main_plan->activity_type = $request->activity_type ?: 'compliance';
                $main_plan->approval_status = 'draft';
                $main_plan->save();
                $plan_data['annual_plan_main_id'] = $main_plan->id;
            }

            $plan = AnnualPlan::create($plan_data);

            if ($request->activity_type == 'performance') {
                $subject_matter_data = [
                    'annual_plan_main_id' => $plan_data['annual_plan_main_id'],
                    'annual_plan_id' => $plan->id,
                    'vumika' => $request->vumika,
                    'subject_matter_en' => $request->subject_matter,
                    'subject_matter_bn' => '',
                    'parent_id' => 0,
                ];

                $subject_matter = ApPsrSubjectMatter::create($subject_matter_data);

                if ($subject_matter->id) {
                    foreach ($request->sub_subject_list as $key => $sub_m) {
                        if ($key != 'undefined') {
                            $ap_sub = new ApPsrSubjectMatter();
                            $ap_sub->parent_id = $subject_matter->id;
                            $ap_sub->annual_plan_main_id = $plan_data['annual_plan_main_id'];
                            $ap_sub->annual_plan_id = $plan->id;
                            $ap_sub->vumika = '';
                            $ap_sub->subject_matter_en = $sub_m['sub_subject_matter'];
                            $ap_sub->subject_matter_bn = '';
                            $log = $ap_sub->save();
                        }
                    }
                }

                $audit_object_data = [
                    'annual_plan_main_id' => $plan_data['annual_plan_main_id'],
                    'annual_plan_id' => $plan->id,
                    'audit_objective_en' => $request->audit_objective,
                    'audit_objective_bn' => $plan->id,
                    'parent_id' => 0,
                ];

                $audit_object = ApPsrAduitObject::create($audit_object_data);

                if ($audit_object->id) {
                    foreach ($request->sub_objective_list as $key => $sub_o) {
                        if ($key != 'undefined') {
                            $sub_object_data = [
                                'annual_plan_main_id' => $plan_data['annual_plan_main_id'],
                                'annual_plan_id' => $plan->id,
                                'audit_objective_en' => $sub_o['sub_objective'],
                                'audit_objective_bn' => '',
                                'parent_id' => $audit_object->id,
                            ];

                            $sub_object = ApPsrAduitObject::create($sub_object_data);
                            if ($sub_object->id) {
                                foreach ($sub_o['line_of_enquires'] as $val) {

                                    $ap_sub = new ApPsrLineOfEnquire();
                                    $ap_sub->sub_objective_id = $sub_object->id;
                                    $ap_sub->line_of_enquire_en = $val;
                                    $ap_sub->line_of_enquire_bn = '';
                                    $ap_sub->save();
                                }
                            }
                        }
                    }
                }
            }


            foreach ($request->milestone_list as $milestone) {
                $ap_milestone = new ApMilestone();
                $ap_milestone->fiscal_year_id = $milestone['fiscal_year_id'];
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
                'total_expenditure' => filter_var(bnToen($request->total_expenditure), FILTER_SANITIZE_NUMBER_INT),
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
                'updated_by' => $cdesk->officer_id,
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

            $plan_datas = AnnualPlan::with('ap_entities')
                ->with('activity.milestones.milestone_calendar')
                ->where('fiscal_year_id', $request->fiscal_year_id)
                ->where('activity_type', $request->activity_type)
                ->where('annual_plan_main_id', $request->annual_plan_main_id)
                ->orderBy('activity_id','ASC')
                ->get()
                ->groupBy('activity_id');

            //            return ['status' => 'success', 'data' => $plan_datas];

            $fiscal_year = XFiscalYear::findOrFail($request->fiscal_year_id, ['start', 'end', 'description'])->toArray();
            $plan_data_final = [];
            $all_ministries = [];
            $annual_plan = [];
            foreach ($plan_datas as $activity_id => $plan_data) {
                //                return ['status' => 'success', 'data' => $plan_data];
                $activity = $plan_data[0]['activity'];

                $ministriesBn = [];
                $ministriesEn = [];
                foreach ($plan_data as $plan) {
                    foreach ($plan['ap_entities'] as $ap_entities) {
                        $ministryBn = $ap_entities['ministry_name_bn'];
                        $ministriesBn[] = $ministryBn;
                        $ministryEn = $ap_entities['ministry_name_en'];
                        $ministriesEn[] = $ministryEn;
                    }
                }

                $ministries = [
                    'ministry_name_en' => implode(' , ', array_unique($ministriesEn)),
                    'ministry_name_bn' => implode(' , ', array_unique($ministriesBn)),
                ];

                $all_ministries[$activity_id] = $ministries;
                //
                $plan_data_final[$activity_id] = ['activity' => $activity] + ['ministries' => $ministries] + ['annual_plans' => $plan_data];
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

            $has_audit_plan =  ApEntityIndividualAuditPlan::where('annual_plan_id', $request->annual_plan_id)->count();

            if ($has_audit_plan) {
                return ['status' => 'error', 'data' => 'Annual Plan Has Individual Audit Plan'];
            }

            AnnualPlan::find($request->annual_plan_id)->delete();
            AnnualPlanEntitie::where('annual_plan_id', $request->annual_plan_id)->delete();
            ApMilestone::where('annual_plan_id', $request->annual_plan_id)->delete();

            $data = ['status' => 'success', 'data' => 'Annual Plan Delete Successfully'];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $data;
    }
}
