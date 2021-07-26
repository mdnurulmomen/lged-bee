<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface OpYearlyAuditCalendarInterface
{
    public function allCalendarLists(Request $request);

    public function changeStatus(Request $request);

    public function saveEventsBeforePublishing(Request $request);
}
