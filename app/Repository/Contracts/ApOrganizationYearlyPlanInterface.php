<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface ApOrganizationYearlyPlanInterface
{
    public function allAnnualPlans(Request $request);
}
