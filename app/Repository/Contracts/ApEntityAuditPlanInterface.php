<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface ApEntityAuditPlanInterface
{
    public function allEntityAuditPlanLists(Request $request);

    public function storeDraftAuditPlan(Request $request);

    public function showEntityAuditPlan(Request $request);
}
