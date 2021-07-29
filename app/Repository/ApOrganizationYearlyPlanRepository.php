<?php

namespace App\Repository;

use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
use App\Repository\Contracts\ApOrganizationYearlyPlanInterface;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class ApOrganizationYearlyPlanRepository implements ApOrganizationYearlyPlanInterface
{
    use GenericData;

    public function allAnnualPlans(Request $request)
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
}
