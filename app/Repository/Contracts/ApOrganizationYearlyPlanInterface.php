<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface ApOrganizationYearlyPlanInterface
{
    public function allAnnualPlans(Request $request);

    public function storeAnnualPlanDetails(Request $request);

    public function storeSelectedRPEntities(Request $request);

    public function allSelectedRPEntities(Request $request);

    public function submitPlanToOCAG(Request $request);
}
