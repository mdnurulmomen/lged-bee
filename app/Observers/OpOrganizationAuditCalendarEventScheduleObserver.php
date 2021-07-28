<?php

namespace App\Observers;

use App\Models\OpOrganizationYearlyAuditCalendarEvent;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;

class OpOrganizationAuditCalendarEventScheduleObserver
{
    /**
     * Handle the OpOrganizationYearlyAuditCalendarEventSchedule "created" event.
     *
     * @param \App\Models\OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule
     * @return void
     */
    public function created(OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule)
    {
        $office_id = $opOrganizationYearlyAuditCalendarEventSchedule->activity_responsible_id;
        $calendar_id = $opOrganizationYearlyAuditCalendarEventSchedule->op_yearly_audit_calendar_id;

        $event_data = OpOrganizationYearlyAuditCalendarEvent::where('office_id', $office_id)->where('op_yearly_audit_calendar_id', $calendar_id)->update(['status' => 'published']);
    }

    /**
     * Handle the OpOrganizationYearlyAuditCalendarEventSchedule "updated" event.
     *
     * @param \App\Models\OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule
     * @return void
     */
    public function updated(OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule)
    {
        //
    }

    /**
     * Handle the OpOrganizationYearlyAuditCalendarEventSchedule "deleted" event.
     *
     * @param \App\Models\OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule
     * @return void
     */
    public function deleted(OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule)
    {
        //
    }

    /**
     * Handle the OpOrganizationYearlyAuditCalendarEventSchedule "restored" event.
     *
     * @param \App\Models\OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule
     * @return void
     */
    public function restored(OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule)
    {
        //
    }

    /**
     * Handle the OpOrganizationYearlyAuditCalendarEventSchedule "force deleted" event.
     *
     * @param \App\Models\OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule
     * @return void
     */
    public function forceDeleted(OpOrganizationYearlyAuditCalendarEventSchedule $opOrganizationYearlyAuditCalendarEventSchedule)
    {
        //
    }
}
