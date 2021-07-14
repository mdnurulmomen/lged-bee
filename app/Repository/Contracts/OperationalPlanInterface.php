<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface OperationalPlanInterface
{
    public function OperationalPlan(Request $request);

    public function OperationalDetail(Request $request);
}
