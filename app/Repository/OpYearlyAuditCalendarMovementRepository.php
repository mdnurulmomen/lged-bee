<?php

namespace App\Repository;

use App\Models\OpYearlyAuditCalendar;
use App\Models\OpYearlyAuditCalendarMovement;
use App\Repository\Contracts\OpYearlyAuditCalendarMovementInterface;
use Illuminate\Http\Request;

class OpYearlyAuditCalendarMovementRepository implements OpYearlyAuditCalendarMovementInterface
{
    public function __construct(OpYearlyAuditCalendarMovement $opYearlyAuditCalendarMovement)
    {
        $this->opYearlyAuditCalendarMovement = $opYearlyAuditCalendarMovement;
    }

    public function forwardAuditCalendar(Request $request): array
    {
        $designations = json_decode($request->designations, true) ?? [];
        $audit_calendar_master_id = $request->audit_calendar_master_id;
        $sent_by = $request->sent_by;
        $opYearlyCalendar = OpYearlyAuditCalendar::select('duration_id', 'fiscal_year_id')->where('id', $audit_calendar_master_id)->first()->toArray();
        $data = [];
        foreach ($designations as $designation) {

            try {
                $movement_data = [
                    'op_yearly_calendar_id' => $audit_calendar_master_id,
                    'duration_id' => $opYearlyCalendar['duration_id'],
                    'fiscal_year_id' => $opYearlyCalendar['fiscal_year_id'],
                    'office_id' => $designation['office_id'],
                    'unit_id' => $designation['unit_id'],
                    'unit_name_en' => $designation['unit_name_en'],
                    'unit_name_bn' => $designation['unit_name_bn'],
                    'officer_type' => $designation['officer_type'],
                    'employee_id' => $designation['officer_id'],
                    'employee_designation_id' => $designation['designation_id'],
                    'employee_designation_en' => $designation['designation_en'],
                    'employee_designation_bn' => $designation['designation_bn'],
                    'user_id' => $designation['officer_id'],
                    'calendar_status' => 'draft',
                    'received_by' => $designation['officer_id'],
                    'sent_by' => $sent_by,
                    'created_by' => $designation['officer_id'],
                    'modified_by' => $designation['officer_id'],
                ];
                OpYearlyAuditCalendarMovement::create($movement_data);
                $data = ['status' => 'success', 'data' => 'Successfully Created'];
            } catch (\Exception $e) {
                $data = ['status' => 'error', 'data' => $e->getMessage()];
            }
        }

        return $data;
    }

    public function movementHistory(Request $request)
    {
        return $this->opYearlyAuditCalendarMovement
            ->where('op_yearly_calendar_id', $request->op_yearly_calendar_id)->get();
    }
}
