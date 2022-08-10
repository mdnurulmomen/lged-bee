<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApEntityIndividualAuditPlanLock;
use App\Models\AuditTemplate;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalendarPlanTeamUpdate;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\OpActivity;
use App\Models\RTemplateContent;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class ApEntityAuditPlanRevisedService
{
    use GenericData;

    public function allEntityAuditPlanLists(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;
        $activity_id = $request->activity_id;
        $cdesk = json_decode($request->cdesk, false);

        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $annualPlanQuery = AnnualPlan::with('annual_plan_main')
                ->with('audit_plans:id,annual_plan_id,fiscal_year_id')
                ->with('ap_entities:id,annual_plan_id,ministry_id,ministry_name_bn,ministry_name_en,entity_id,entity_name_bn,entity_name_en')
                ->with('audit_plans.office_order:id,audit_plan_id,approved_status')
                ->select('id','annual_plan_main_id','activity_id','fiscal_year_id','office_type','total_unit_no',
                    'subject_matter','has_dc_schedule','status','created_at','project_id','project_name_en','project_name_bn')
                ->where('fiscal_year_id', $fiscal_year_id)
                ->where('activity_id', $activity_id)
                ->whereNull('is_revised');

            if ($request->per_page && $request->page && !$request->all) {
                $annualPlanQuery = $annualPlanQuery->paginate($request->per_page);
            } else {
                $annualPlanQuery = $annualPlanQuery->get();
            }
            return ['status' => 'success', 'data' => $annualPlanQuery];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPreviouslyAssignedDesignations(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $designations = AuditVisitCalenderPlanMember::where('fiscal_year_id', $request->fiscal_year_id)->where('activity_id', $request->activity_id)->where('team_member_office_id', $request->office_id)->pluck('team_member_designation_id')->toArray();
            $designations = implode(',', $designations);
            return ['status' => 'success', 'data' => $designations];
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

            $annual_plan = AnnualPlan::with('ap_entities')->where('id', $request->annual_plan_id)->with('fiscal_year')->first()->toArray();
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
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $individual_audit_plan = ApEntityIndividualAuditPlan::with(['fiscal_year','office_order','annual_plan','annual_plan.ap_entities','audit_teams'])->find($request->audit_plan_id)->toArray();
            return ['status' => 'success', 'data' => $individual_audit_plan];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function update(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $this->switchOffice($cdesk->office_id);

        \DB::beginTransaction();
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
                'status' => $request->status,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
                'device_type' => null,
                'device_id' => null,
            ];

            /*\Log::info(json_encode($draft_plan_data));*/

            if ($request->has('audit_plan_id') && $request->audit_plan_id > 0) {
                $audit_plan_id = $request->audit_plan_id;
                $draft_plan = ApEntityIndividualAuditPlan::find($audit_plan_id)
                    ->update($draft_plan_data);
                $draft_plan = ApEntityIndividualAuditPlan::find($audit_plan_id);

                //delete template content
                $templated_type = 'individual_plan';
                RTemplateContent::where('relational_id',$audit_plan_id)->where('template_type', $templated_type)->delete();
            } else {
                $draft_plan = ApEntityIndividualAuditPlan::create($draft_plan_data);
                $audit_plan_id = $draft_plan->id;
            }

            //template content
            $contents = [];
            $content_list = json_decode(gzuncompress(getDecryptedData($request->plan_description)),true);

            foreach (json_decode($content_list, true) as $content) {
                $contents[] = [
                    'relational_id' => $audit_plan_id,
                    'template_type' => 'individual_plan',
                    'content_key' => $content['content_key'] ?? 'old_plan',
                    'content_value' => base64_encode($content['content']),
                ];
            }
            RTemplateContent::insert($contents);

            \DB::commit();
            $data = ['status' => 'success', 'data' => $draft_plan];
        } catch (\Exception $e) {
            \DB::rollback();
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();

        return $data;
    }

    public function getAuditPlanWiseTeam(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $team_log =  AuditVisitCalendarPlanTeamUpdate::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('activity_id', $request->activity_id)
                ->where('audit_plan_id', $request->audit_plan_id)
                ->where('annual_plan_id', $request->annual_plan_id)
                ->count();

            $query = $team_log > 0 ? AuditVisitCalendarPlanTeamUpdate::query() : AuditVisitCalendarPlanTeam::query();

            $query->where('fiscal_year_id', $request->fiscal_year_id);
            $query->where('activity_id', $request->activity_id);
            $query->where('audit_plan_id', $request->audit_plan_id);
            $query->where('annual_plan_id', $request->annual_plan_id);
            $teams = $query->get()->toArray();

            $data = ['status' => 'success', 'data' => $teams];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $data;
    }

    public function getTeamInfo(Request $request)
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $data = AuditVisitCalendarPlanTeam::with('child')->where('id', $request->team_id)->get()->toArray();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    //get annual/audit plan wise team members
    public function getPlanWiseTeamMembers(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $teamMembers = AuditVisitCalenderPlanMember::distinct()
                ->select('team_member_name_bn','team_member_name_en','team_member_designation_bn',
                    'team_member_designation_en','team_member_role_bn','team_member_role_en','mobile_no','employee_grade')
                ->where('audit_plan_id',$request->audit_plan_id)
                ->orderBy('employee_grade','ASC')
                ->get()
                ->toArray();
            return ['status' => 'success', 'data' => $teamMembers];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    //get annual/audit plan wise team schedules
    public function getPlanWiseTeamSchedules(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $teamSchedule = AuditVisitCalendarPlanTeam::where('audit_plan_id',$request->audit_plan_id)
                ->get()
                ->toArray();
            return ['status' => 'success', 'data' => $teamSchedule];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function auditPlanAuditEditLock(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $user_details = $cdesk->officer_bn.','.$cdesk->designation_bn;

//            return ['status' => 'error', 'data' => $cdesk->officer_id];

            $edit_lock = ApEntityIndividualAuditPlan::find($request->audit_plan_id);

            if($edit_lock->edit_time_start){
                $start = strtotime($edit_lock->edit_time_start);
                $end = strtotime(now());
                $mins = ($end - $start) / 60;
                if($mins > 30){
                    $edit_lock->edit_employee_id = $cdesk->officer_id;
                    $edit_lock->edit_user_details = $user_details;
                    $edit_lock->edit_time_start = now();
                    $edit_lock->save();
                    return ['status' => 'success', 'data' => true];
                }else{
                    if($cdesk->officer_id == $edit_lock->edit_employee_id){
                        return ['status' => 'success', 'data' => true];
                    }else{
                        return ['status' => 'success', 'data' => false];
                    }
                }
            }else{
                $edit_lock->edit_employee_id = $cdesk->officer_id;
                $edit_lock->edit_user_details = $user_details;
                $edit_lock->edit_time_start = now();
                $edit_lock->save();
                return ['status' => 'success', 'data' => true];
            }

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
