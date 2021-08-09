<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface EditHistoryInterface
{
    public function addOpYearlyAuditCalendarEditHistory($cdesk, $changed_data);
}
