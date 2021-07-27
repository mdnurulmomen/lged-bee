<?php

namespace App\Observers;

use App\Models\OpActivityComment;
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
        //
    }

    /**
     * Handle the OpActivityComment "updated" event.
     *
     * @param \App\Models\OpActivityComment $opActivityComment
     * @return void
     */
    public function updated(OpActivityComment $opActivityComment)
    {
        //
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
