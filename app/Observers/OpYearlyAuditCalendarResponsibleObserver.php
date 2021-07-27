<?php

namespace App\Observers;

use App\Models\OpYearlyAuditCalendarResponsible;
use Illuminate\Http\Request;

class OpYearlyAuditCalendarResponsibleObserver
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the OpYearlyAuditCalendarResponsible "created" event.
     *
     * @param \App\Models\OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible
     * @return void
     */
    public function created(OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible)
    {
        //
    }

    /**
     * Handle the OpYearlyAuditCalendarResponsible "updated" event.
     *
     * @param \App\Models\OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible
     * @return void
     */
    public function updated(OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible)
    {
        //
    }

    /**
     * Handle the OpYearlyAuditCalendarResponsible "deleted" event.
     *
     * @param \App\Models\OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible
     * @return void
     */
    public function deleted(OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible)
    {
        //
    }

    /**
     * Handle the OpYearlyAuditCalendarResponsible "restored" event.
     *
     * @param \App\Models\OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible
     * @return void
     */
    public function restored(OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible)
    {
        //
    }

    /**
     * Handle the OpYearlyAuditCalendarResponsible "force deleted" event.
     *
     * @param \App\Models\OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible
     * @return void
     */
    public function forceDeleted(OpYearlyAuditCalendarResponsible $opYearlyAuditCalendarResponsible)
    {
        //
    }
}
