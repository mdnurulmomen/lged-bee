<?php

namespace App\Repository;

use App\Models\AnnualPlanApproval;
use App\Models\OpActivity;
use App\Models\OpOrganizationYearlyAuditCalendarEvent;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
use App\Models\OpYearlyAuditCalendar;
use App\Models\OpYearlyAuditCalendarActivity;
use App\Models\OpYearlyAuditCalendarResponsible;
use App\Models\XFiscalYear;
use App\Models\XResponsibleOffice;
use App\Repository\Contracts\OpYearlyAuditCalendarInterface;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                "status" => $yearly_audit_calendar['status'],
                'fiscal_year' => $yearly_audit_calendar['fiscal_year']['description'],
                'approvers' => $approvers,
                'editors' => $editors,
                'viewers' => $viewers,
            ];
        }


        return $data;
    }

    public function yearsToCreateCalendar()
    {
        try {
            $created_calendar_years = OpYearlyAuditCalendar::select('fiscal_year_id')->get()->toArray();
            return XFiscalYear::select('id AS fiscal_year_id', 'description')->whereNotIn('id', $created_calendar_years)->get()->toArray();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function storeOpYearlyAuditCalendar(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;
        $cdesk = json_decode($request->cdesk, false);

        DB::beginTransaction();
        try {
            $is_exist = $this->opYearlyAuditCalendar::select('id')->where('fiscal_year_id', $fiscal_year_id)->get();
            if (count($is_exist) > 0) {
                throw new \Exception('Calendar Already Exist.', 409);
            }
            $fiscal_year_data = XFiscalYear::select('id', 'duration_id')->where('id', $fiscal_year_id)->first()->toArray();

            $op_yearly_audit_calendar_data = [
                'duration_id' => $fiscal_year_data['duration_id'],
                'fiscal_year_id' => $fiscal_year_data['id'],
                'employee_record_id' => $cdesk->officer_id,
                'initiator_name_en' => $cdesk->officer_en,
                'initiator_name_bn' => $cdesk->officer_bn,
                'initiator_unit_name_en' => $cdesk->office_unit_en,
                'initiator_unit_name_bn' => $cdesk->office_unit_bn,
                'status' => 'draft',
            ];

            $op_yearly_audit_calendar = $this->opYearlyAuditCalendar->create($op_yearly_audit_calendar_data);

            $activity_milestones = OpActivity::where('fiscal_year_id', $fiscal_year_id)->where('is_activity', 1)->with('milestones')->get()->toArray();

            foreach ($activity_milestones as $activity_milestone) {
                foreach ($activity_milestone['milestones'] as $milestone) {
                    $op_yearly_audit_calendar_activities_data = [
                        'op_yearly_audit_calendar_id' => $op_yearly_audit_calendar['id'],
                        'duration_id' => $activity_milestone['duration_id'],
                        'fiscal_year_id' => $activity_milestone['fiscal_year_id'],
                        'outcome_id' => $activity_milestone['outcome_id'],
                        'output_id' => $activity_milestone['output_id'],
                        'activity_id' => $activity_milestone['id'],
                        'milestone_id' => $milestone['id'],
                    ];

                    OpYearlyAuditCalendarActivity::create($op_yearly_audit_calendar_activities_data);
                }
            }
            DB::commit();
            return ['status' => 'success', 'data' => 'Successfully Created', 'code' => '204'];
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $exception->getMessage(), 'code' => $exception->getCode()];
        }
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

    public function pendingEventsForPublishing(Request $request): array
    {
        $calendar_events = OpOrganizationYearlyAuditCalendarEvent::select('id')->where('op_yearly_audit_calendar_id', $request->calendar_id)->get()->toArray();

        if (count($calendar_events) == 0) {
            $is_saved = $this->saveEventsBeforePublishing($request);
        }
        $calendar_pending_events = OpOrganizationYearlyAuditCalendarEvent::select('id AS event_id', 'office_id', 'status', 'activity_count', 'milestone_count')->where('op_yearly_audit_calendar_id', $request->calendar_id)->where('status', 0)->with('office')->get()->toArray();
        if ($calendar_pending_events) {
            return responseFormat('success', $calendar_pending_events);
        } else {
            return responseFormat('error', $calendar_pending_events);
        }
    }

    public function yearlyAuditCalendarEventList(Request $request): array
    {
        $calendar_id = OpYearlyAuditCalendar::where('fiscal_year_id',$request->fiscal_year_id)->first()->id;

//        $calendar_events = OpOrganizationYearlyAuditCalendarEvent::select('id AS event_id', 'office_id', 'status',
//            'approval_status', 'activity_count', 'milestone_count')
//            ->where('op_yearly_audit_calendar_id', $calendar_id)
//            ->where('approval_status','!=','draft')
//            ->with('office')
//            ->get()
//            ->toArray();
        $calendar_events = AnnualPlanApproval::where('fiscal_year_id',$request->fiscal_year_id)->where('activity_type',$request->activity_type)->get();

        if ($calendar_events) {
            return responseFormat('success', $calendar_events);
        } else {
            return responseFormat('error', $calendar_events);
        }
    }

    public function saveEventsBeforePublishing(Request $request): array
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
                        'activity_type' => $responsible['activities']['activity_type'],
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
                        'audit_calendar_data' => json_encode($directory),
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

    /**
     * @throws \Exception
     */
    public function publishPendingEvents(Request $request): array
    {
        $office_ids = $request->office_ids;
        $calendar_id = $request->calendar_id;

        $data = [];
        $error = [];
        $success = [];

        foreach ($office_ids as $office_id) {
            $pending_events = OpOrganizationYearlyAuditCalendarEvent::select('id', 'audit_calendar_data')->where('office_id', $office_id)->where('op_yearly_audit_calendar_id', $calendar_id)->latest()->first();
            if ($pending_events) {
                $pending_events_calendar_data = $pending_events->audit_calendar_data;
                $arr_pending_event = json_decode($pending_events_calendar_data, true);
                $arr_pending_event_activities = $arr_pending_event['activities'];
                if ($office_id == $arr_pending_event['office_id']) {
                    $common_data = [
                        'op_audit_calendar_event_id' => $pending_events->id,
                        'activity_responsible_id' => $arr_pending_event['office_id'],
                        'duration_id' => $arr_pending_event['duration_id'],
                        'fiscal_year_id' => $arr_pending_event['fiscal_year_id'],
                        'op_yearly_audit_calendar_id' => $arr_pending_event['op_yearly_audit_calendar_id'],
                    ];
                    try {
                        $connection = $this->switchOffice($office_id);
                        if (isSuccessResponse($connection)) {
                            DB::beginTransaction();
                            foreach ($arr_pending_event_activities as $arr_pending_event_activity) {
                                foreach ($arr_pending_event_activity['milestones'] as $milestone) {
                                    $schedule_activity_data = [
                                        'outcome_id' => $arr_pending_event_activity['outcome_id'],
                                        'output_id' => $arr_pending_event_activity['output_id'],
                                        'activity_id' => $arr_pending_event_activity['activity_id'],
                                        'activity_type' => $arr_pending_event_activity['activity_type'],
                                        'activity_title_en' => $arr_pending_event_activity['activity_title_en'],
                                        'activity_title_bn' => $arr_pending_event_activity['activity_title_bn'],
                                        'op_yearly_audit_calendar_activity_id' => $arr_pending_event_activity['op_yearly_audit_calendar_activity_id'],
                                        'activity_milestone_id' => $milestone['milestone_id'],
                                        'milestone_title_en' => $milestone['milestone_title_en'],
                                        'milestone_title_bn' => $milestone['milestone_title_bn'],
                                        'milestone_target' => $milestone['target_date'],
                                    ];
                                    $schedule_data = $common_data + $schedule_activity_data;
                                    $created_schedule = OpOrganizationYearlyAuditCalendarEventSchedule::create($schedule_data);
                                }
                                $success[$office_id] = ['office_id' => $office_id, 'data' => 'Successfully Published to Office'];
                            }
                            DB::commit();
                        } else {
                            $error[$office_id] = ['db_error' => 'অফিস ডাটাবেজ পাওয়া যায় নি! সাপোর্ট টিমের সাথে যোগাযোগ করুন।'];
                        }
                    } catch (\Exception $exception) {
                        DB::rollBack();
                        $error[$office_id] = ['exception' => $exception->getMessage(), ['code' => $exception->getCode()]];
                    }
                }
            } else {
                $error[$office_id] = ['office_id' => $office_id, 'event_error' => 'Calendar For Office Not Found'];
            }
        }

        return [
            'success' => $success,
            'error' => $error,
        ];
    }

    public function storeActivityResponsible($data): bool
    {
        $auditCalendar = OpYearlyAuditCalendarActivity::select('id', 'duration_id', 'fiscal_year_id', 'outcome_id', 'output_id', 'activity_id', 'op_yearly_audit_calendar_id')
            ->where('activity_id', $data['activity_id'])->first()->toArray();
        $auditCalendar['op_yearly_audit_calendar_activity_id'] = $auditCalendar['id'];
        unset($auditCalendar['id']);

        OpYearlyAuditCalendarResponsible::where('activity_id', $data['activity_id'])->delete();

        foreach ($data['selected_office_ids'] as $responsible_id) {
            if ($responsible_id) {
                $office = XResponsibleOffice::select("office_id", "office_layer", "office_name_en", "office_name_bn", "short_name_en", "short_name_bn")->where('id', $responsible_id)->first()->toArray();
                $creatingData = array_merge($office, $auditCalendar);
                OpYearlyAuditCalendarResponsible::create($creatingData);
            }
        }

        return true;
    }
}
