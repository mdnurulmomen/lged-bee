<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\AuditTemplate;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\OpActivity;
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

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $annualPlanQuery = AnnualPlan::with('audit_plans:id,annual_plan_id,fiscal_year_id')
                ->with('ap_entities:id,annual_plan_id,ministry_id,ministry_name_bn,ministry_name_en,entity_id,entity_name_bn,entity_name_en')
                ->with('audit_plans.office_order:id,audit_plan_id,approved_status')
                ->select('id','activity_id','fiscal_year_id','office_type','total_unit_no',
                    'subject_matter','has_dc_schedule','created_at')
                ->where('fiscal_year_id', $fiscal_year_id)
                ->where('activity_id', $activity_id);

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
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $audit_template = ApEntityIndividualAuditPlan::with(['annual_plan','annual_plan.ap_entities'])->find($request->audit_plan_id)->toArray();
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
                'status' => $request->status,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
                'device_type' => '',
                'device_id' => '',
            ];

            \Log::info(json_encode($draft_plan_data));

            if ($request->has('audit_plan_id') && $request->audit_plan_id > 0) {
                $draft_plan = ApEntityIndividualAuditPlan::find($request->audit_plan_id)
                    ->update($draft_plan_data);
                $draft_plan = ApEntityIndividualAuditPlan::find($request->audit_plan_id);
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

    public function getAuditPlanWiseTeam(Request $request): array
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
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
