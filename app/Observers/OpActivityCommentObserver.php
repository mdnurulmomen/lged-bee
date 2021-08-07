<?php

namespace App\Observers;

use App\Models\OpActivityComment;
use App\Services\OpYearlyAuditCalendarServices;
use Illuminate\Http\Request;

class OpActivityCommentObserver
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the OpActivityComment "created" event.
     *
     * @param \App\Models\OpActivityComment $opActivityComment
     * @return void
     */
    public function created(OpActivityComment $opActivityComment)
    {
        $calendar_service = new OpYearlyAuditCalendarServices();
        $data = [
            'duration_id' => $opActivityComment->activity->duration_id,
            'fiscal_year_id' => $opActivityComment->activity->fiscal_year_id,
            'activity_id' => $opActivityComment->activity_id,
            'op_yearly_calendar_id' => $opActivityComment->activity->calendar_activity[0]->op_yearly_audit_calendar_id,
            'old_data' => json_encode(['comment_en' => $opActivityComment->comment_en, 'comment_bn' => $opActivityComment->comment_bn]),
        ];
        $calendar_service->editHistoryCreateService($this->request->cdesk, $data);
    }

    /**
     * Handle the OpActivityComment "updated" event.
     *
     * @param \App\Models\OpActivityComment $opActivityComment
     * @return void
     */
    public function updated(OpActivityComment $opActivityComment)
    {
        $calendar_service = new OpYearlyAuditCalendarServices();
        $data = [
            'duration_id' => $opActivityComment->activity->duration_id,
            'fiscal_year_id' => $opActivityComment->activity->fiscal_year_id,
            'activity_id' => $opActivityComment->activity_id,
            'op_yearly_calendar_id' => $opActivityComment->activity->calendar_activity[0]->op_yearly_audit_calendar_id,
            'old_data' => json_encode(['comment_en' => $opActivityComment->comment_en, 'comment_bn' => $opActivityComment->comment_bn]),
        ];
        $calendar_service->editHistoryCreateService($this->request->cdesk, $data);
    }

    /**
     * Handle the OpActivityComment "deleted" event.
     *
     * @param \App\Models\OpActivityComment $opActivityComment
     * @return void
     */
    public function deleted(OpActivityComment $opActivityComment)
    {
        //
    }

    /**
     * Handle the OpActivityComment "restored" event.
     *
     * @param \App\Models\OpActivityComment $opActivityComment
     * @return void
     */
    public function restored(OpActivityComment $opActivityComment)
    {
        //
    }

    /**
     * Handle the OpActivityComment "force deleted" event.
     *
     * @param \App\Models\OpActivityComment $opActivityComment
     * @return void
     */
    public function forceDeleted(OpActivityComment $opActivityComment)
    {
        //
    }
}
