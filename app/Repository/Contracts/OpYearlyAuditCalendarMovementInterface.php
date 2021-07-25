<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface OpYearlyAuditCalendarMovementInterface
{
    public function forwardAuditCalendar(Request $request);
    public function movementHistory(Request $request);
}
