<?php

namespace App\Repository;

use App\Models\OpOrganizationYearlyAuditCalendarEvent;
use App\Models\OpYearlyAuditCalendar;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Repository\Contracts\OpYearlyAuditCalendarInterface;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class OpYearlyAuditCalendarRepository implements OpYearlyAuditCalendarInterface
{
    use GenericData;

    public function __construct(OpYearlyAuditCalendar $opYearlyAuditCalendar)
    {
        $this->opYearlyAuditCalendar = $opYearlyAuditCalendar;
    }

    public function allCalendarLists(Request $request): array
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

    public function changeStatus(Request $request)
    {
        $status = $request->status == 'approved' ? 'approved' : ($request->status == 'published' ? 'published' : 'draft');
        try {
            $this->opYearlyAuditCalendar->where('id', $request->id)->update(['status' => $status]);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pendingEventsForPublishing(Request $request)
    {
        $calendar_events = OpOrganizationYearlyAuditCalendarEvent::select('id')->where('op_yearly_audit_calendar_id', $request->calendar_id)->get()->toArray();

        if (count($calendar_events) == 0) {
            $is_saved = $this->saveEventsBeforePublishing($request);
        }
        $calendar_pending_events = OpOrganizationYearlyAuditCalendarEvent::select('id AS event_id', 'office_id', 'status', 'activity_count', 'milestone_count')->where('op_yearly_audit_calendar_id', $request->calendar_id)->where('status', 0)->with('office')->get()->toArray();
        return $calendar_pending_events;
    }

    public function saveEventsBeforePublishing(Request $request)
    {
        $data = [];
        $milestones = [];
        $ac_array = [];
        $responsibles = OpYearlyAuditCalendarResponsible::where('op_yearly_audit_calendar_id', $request->calendar_id)->with('activities.milestones')->with('activities.comment')->orderBy('office_id')->orderBy('activity_id')->get()->groupBy('office_id')->toArray();

        if ($responsibles) {
            foreach ($responsibles as $office_id => $resp) {
                unset($ac_array);
                $activity_count = 0;
                $milestone_count = 0;
                foreach ($resp as $responsible) {
                    $activity_count += 1;
                    unset($milestones);
                    $common_data = [
                        'office_id' => $responsible['office_id'],
                        'duration_id' => $responsible['duration_id'],
                        'fiscal_year_id' => $responsible['fiscal_year_id'],
                        'op_yearly_audit_calendar_id' => $responsible['op_yearly_audit_calendar_id'],
                    ];

                    foreach ($responsible['activities']['milestones'] as $milestone) {
                        $milestone_count += 1;
                        $milestones[] = [
                            'milestone_id' => $milestone['id'],
                            'milestone_title_en' => $milestone['title_en'],
                            'milestone_title_bn' => $milestone['title_bn'],
                            'target_date' => $this->milestoneTargetDate($milestone['id']),
                        ];
                    }

                    $ac_array[] = [
                        'activity_responsible_id' => $office_id,
                        'output_id' => $responsible['activities']['output_id'],
                        'outcome_id' => $responsible['activities']['outcome_id'],
                        'op_yearly_audit_calendar_activity_id' => $responsible['op_yearly_audit_calendar_activity_id'],
                        'activity_id' => $responsible['activities']['id'],
                        'activity_title_en' => $responsible['activities']['title_en'],
                        'activity_title_bn' => $responsible['activities']['title_bn'],
                        'comment' => $responsible['activities']['comment'],
                        'milestones' => $milestones,
                    ];
                    $activity_data['activities'] = $ac_array;
                }
                $count = ['activity_count' => $activity_count, 'milestone_count' => $milestone_count];
                $data[$office_id] = $common_data + $activity_data + $count;
            }

            foreach ($data as $directory) {
                try {
                    $event_data = [
                        'office_id' => $directory['office_id'],
                        'op_yearly_audit_calendar_id' => $directory['op_yearly_audit_calendar_id'],
                        'activity_count' => $directory['activity_count'],
                        'milestone_count' => $directory['milestone_count'],
                        'audit_calendar_data' => json_encode($directory['activities']),
                        'status' => 'pending',
                    ];
                    OpOrganizationYearlyAuditCalendarEvent::create($event_data);
                } catch (\Exception $exception) {
                    return ['status' => 'error', 'data' => $exception];
                }
            }
        }
        return $data;
//        return ['status' => 'success'];
    }
}
