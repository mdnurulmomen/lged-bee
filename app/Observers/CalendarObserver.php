<?php

namespace App\Observers;

use App\Models\OpYearlyAuditCalendarActivity;
use App\Models\OpYearlyAuditCalendarEditHistory;

class CalendarObserver
{
    /**
     * Handle the OpYearlyAuditCalendarActivity "save" event.
     *
     * @param  \App\Models\OpYearlyAuditCalendarActivity  $opYearlyAuditCalendarActivity
     * @return void
     */
    public function saving(OpYearlyAuditCalendarActivity $op)
    {
        $history = new OpYearlyAuditCalendarEditHistory;
        $history->op_yearly_calendar_id = $op->op_yearly_calendar_id;
        $history->duration_id = $op->duration_id;
        $history->fiscal_year_id = $op->fiscal_year_id;
        $history->unit_id = $op->unit_id;
        $history->employee_id = $op->employee_id;
        $history->user_id = $op->user_id;
        $history->employee_designation_id = $op->employee_designation_id;
        $history->old_data = json_encode($op->target_date);
        $history->save();
    }
}
