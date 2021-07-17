<?php

namespace App\Repository;

use App\Models\OpYearlyAuditCalendar;
use App\Repository\Contracts\OpYearlyAuditCalendarInterface;
use Illuminate\Http\Request;

class OpYearlyAuditCalendarRepository implements OpYearlyAuditCalendarInterface
{
    public function __construct(OpYearlyAuditCalendar $opYearlyAuditCalendar)
    {
        $this->opYearlyAuditCalendar = $opYearlyAuditCalendar;
    }

    public function allCalendarLists(Request $request)
    {
        if ($request->per_page && $request->page && !$request->all) {
            $yearly_audit_calendars = $this->opYearlyAuditCalendar->with(['calendar_movements', 'fiscal_year'])->paginate($request->per_page)->toArray();
        } else {
            $yearly_audit_calendars = $this->opYearlyAuditCalendar->with(['calendar_movements', 'fiscal_year'])->get()->toArray();
        }

        $data = [];
        $approvers = [];
        $editors = [];
        $viewers = [];
        foreach ($yearly_audit_calendars as $yearly_audit_calendar) {
            $recipients = $yearly_audit_calendar['calendar_movements'];
            foreach ($recipients as $recipient) {
                if ($recipient['officer_type'] == 'approver') {
                    $approvers[] = $recipient;
                } elseif ($recipient['officer_type'] == 'editor') {
                    $editors[] = $recipient;
                } elseif ($recipient['officer_type'] == 'viewer') {
                    $viewers[] = $recipient;
                }
            }
            $data[] = [
                "id" => $yearly_audit_calendar['id'],
                "duration_id" => $yearly_audit_calendar['duration_id'],
                "fiscal_year_id" => $yearly_audit_calendar['fiscal_year_id'],
                "employee_record_id" => $yearly_audit_calendar['employee_record_id'],
                "initiator_name_en" => $yearly_audit_calendar['initiator_name_en'],
                "initiator_name_bn" => $yearly_audit_calendar['initiator_name_bn'],
                "initiator_unit_name_en" => $yearly_audit_calendar['initiator_unit_name_en'],
                "initiator_unit_name_bn" => $yearly_audit_calendar['initiator_unit_name_bn'],
                "cdesk_name_en" => $yearly_audit_calendar['cdesk_name_en'],
                "cdesk_name_bn" => $yearly_audit_calendar['cdesk_name_bn'],
                "cdesk_unit_name_en" => $yearly_audit_calendar['cdesk_unit_name_en'],
                "cdesk_unit_name_bn" => $yearly_audit_calendar['cdesk_unit_name_bn'],
                "status" => $yearly_audit_calendar['status'],
                'fiscal_year' => $yearly_audit_calendar['fiscal_year']['description'],
                'approvers' => $approvers,
                'editors' => $editors,
                'viewers' => $viewers,
            ];
        }


        return $data;
    }
}
