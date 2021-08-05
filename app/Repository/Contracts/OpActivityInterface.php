<?php

namespace App\Repository\Contracts;

use Illuminate\Http\Request;

interface OpActivityInterface
{
    public function allActivities(Request $request);

    public function findActivities(Request $request);

    public function showActivitiesByFiscalYear(Request $request);

    public function showActivityMilestones(Request $request);

    public function storeActivity($validated_data);
}
