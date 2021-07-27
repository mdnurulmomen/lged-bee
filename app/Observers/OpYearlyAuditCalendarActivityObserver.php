<?php

namespace App\Observers;

use App\Models\OpYearlyAuditCalendarActivity;
use App\Models\OpYearlyAuditCalendarEditHistory;
use Illuminate\Http\Request;

class OpYearlyAuditCalendarActivityObserver
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the OpYearlyAuditCalendarActivity "update" event.
     *
     * @param \App\Models\OpYearlyAuditCalendarActivity $opYearlyAuditCalendarActivity
     * @return void
     */
    public function updated(OpYearlyAuditCalendarActivity $opYearlyAuditCalendarActivity)
    {
        $history = new OpYearlyAuditCalendarEditHistory;
        $history->op_yearly_calendar_id = $opYearlyAuditCalendarActivity->op_yearly_audit_calendar_id;
        $history->duration_id = $opYearlyAuditCalendarActivity->duration_id;
        $history->fiscal_year_id = $opYearlyAuditCalendarActivity->fiscal_year_id;
        $history->unit_id = $this->request->unit_id;
        $history->employee_id = $this->request->employee_id;
        $history->user_id = $this->request->user_id;
        $history->employee_designation_id = $this->request->employee_designation_id;
        $history->old_data = json_encode(['target_date' => $opYearlyAuditCalendarActivity->target_date]);
        $history->save();
    }
}
