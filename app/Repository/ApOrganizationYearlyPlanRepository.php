<?php

namespace App\Repository;

use App\Models\ApOrganizationYearlyPlanBudget;
use App\Models\ApOrganizationYearlyPlanResponsibleParty;
use App\Models\ApOrganizationYearlyPlanStaff;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
use App\Repository\Contracts\ApOrganizationYearlyPlanInterface;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class ApOrganizationYearlyPlanRepository implements ApOrganizationYearlyPlanInterface
{
    use GenericData;

    public function allAnnualPlans(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;
        $cdesk = json_decode($request->cdesk, false);

        try {
            $this->switchOffice($cdesk->office_id);

            $schedules = OpOrganizationYearlyAuditCalendarEventSchedule::where('fiscal_year_id', $fiscal_year_id)
                ->where('activity_responsible_id', $cdesk->office_id)
                ->select('id AS schedule_id', 'fiscal_year_id', 'activity_id', 'activity_title_en', 'activity_title_bn', 'activity_responsible_id AS office_id', 'activity_milestone_id', 'op_yearly_audit_calendar_activity_id', 'op_yearly_audit_calendar_id', 'milestone_title_en', 'milestone_title_bn', 'milestone_target')
                ->with(['assigned_staffs', 'assigned_budget', 'assigned_rp'])
                ->get()
                ->groupBy('activity_id')
                ->toArray();

            $data = ['status' => 'success', 'data' => $schedules];
        } catch (\Exception $exception) {
            $data = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $data;

    }

    public function storeAnnualPlanDetails(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $designations = json_decode($request->designations, true) ?? [];
        $data = [];
        $this->switchOffice($cdesk->office_id);
        foreach ($designations as $designation) {
            try {
                $staff_data = [
                    'schedule_id' => $request->schedule_id,
                    'activity_id' => $request->activity_id,
                    'milestone_id' => $request->milestone_id,
                    'office_id' => $designation['office_id'],
                    'unit_id' => $designation['unit_id'],
                    'unit_name_en' => $designation['unit_name_en'],
                    'unit_name_bn' => $designation['unit_name_bn'],
                    'employee_name_en' => $designation['officer_name_en'],
                    'employee_name_bn' => $designation['officer_name_bn'],
                    'employee_category' => $designation['officer_category'],
                    'employee_grade' => $designation['officer_grade'],
                    'employee_id' => $designation['officer_id'],
                    'designation_id' => $designation['designation_id'],
                    'employee_designation_en' => $designation['designation_en'],
                    'employee_designation_bn' => $designation['designation_bn'],
                    'task_start_date_plan' => $request->start_date,
                    'task_end_date_plan' => $request->end_date,
                ];
                ApOrganizationYearlyPlanStaff::create($staff_data);
                $data = ['status' => 'success', 'data' => 'Successfully Created'];
            } catch (\Exception $e) {
                $data = ['status' => 'error', 'data' => $e->getMessage()];
            }
        }

        $budget_data = [
            'schedule_id' => $request->schedule_id,
            'activity_id' => $request->activity_id,
            'milestone_id' => $request->milestone_id,
            'budget' => $request->budget,
        ];
        ApOrganizationYearlyPlanBudget::updateOrCreate(['milestone_id' => $request->milestone_id], $budget_data);

        $this->emptyOfficeDBConnection();

        return $data;
    }

    public function storeSelectedRPEntities(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
    }

    public function allSelectedRPEntities(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $this->switchOffice($cdesk->office_id);

        try {
            $all_rp = ApOrganizationYearlyPlanResponsibleParty::where('schedule_id', $request->schedule_id)->where('activity_id', $request->activity_id)->where('milestone_id', $request->milestone_id)->with(['staffs', 'budget'])->get();

            foreach ($all_rp as $rp) {
                $all_rp_data[] = [
                    'id' => $rp->id,
                    'schedule_id' => $rp->schedule_id,
                    'milestone_id' => $rp->milestone_id,
                    'activity_id' => $rp->activity_id,
                    'party_id' => $rp->party_id,
                    'party_name_en' => $rp->party_name_en,
                    'party_name_bn' => $rp->party_name_bn,
                    'party_type' => $rp->party_type,
                    'task_start_date_plan' => $rp->task_start_date_plan,
                    'task_end_date_plan' => $rp->task_end_date_plan,
                    'staff_count' => $rp->staffs->count(),
                    'budget' => $rp->budget->budget,
                ];
            }

            $data = ['status' => 'success', 'data' => $all_rp_data];
        } catch (\Exception $e) {
            $data = ['status' => 'error', 'data' => $e->getMessage()];
        }
        $this->emptyOfficeDBConnection();

        return $data;
    }

}
