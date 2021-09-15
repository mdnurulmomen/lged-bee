<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AuditVisitCalenderPlan;
use App\Models\OpActivity;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Models\XFiscalYear;
use App\Models\XResponsibleOffice;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class AuditVisitCalendarPlanService
{
    use GenericData;

    public function getIndividualPlanCalendar(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $calendar = AuditVisitCalenderPlan::where('team_member_designation_id', $cdesk->designation_id)->where('team_member_officer_id', $cdesk->officer_id)->where('team_member_office_id', $cdesk->office_id)->get();
            return ['status' => 'success', 'data' => $calendar];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }


    }

    public function storeIndividualPlanCalendar(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try{
            $visit_data = [
                'fiscal_year_id',
                'duration_id',
                'outcome_id',
                'output_id',
                'activity_id',
                'milestone_id',
                'annual_plan_id',
                'audit_plan_id',
                'ministry_id',
                'cost_center_id',
                'cost_center_name_en',
                'cost_center_name_bn',
                'team_id',
                'team_start_date',
                'team_end_date',
                'team_member_start_date',
                'team_member_end_date',
                'team_member_name_en',
                'team_member_name_bn',
                'team_member_designation_en',
                'team_member_designation_bn',
                'team_member_role_en',
                'team_member_role_bn',
                'team_member_activity',
                'team_member_activity_description',
                'activity_location',
                'activity_man_days',
                'mobile_no',
                'fiscal_year',
                'approve_status',
            ];

            $create = AuditVisitCalenderPlan::create($visit_data);
            $this->emptyOfficeDBConnection();
            return $data = ['status' => 'success', 'data' => 'Created!'];
        }catch (\Exception $exception){
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }


    }
}
