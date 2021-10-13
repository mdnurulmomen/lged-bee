<?php

namespace App\Http\Controllers;

use App\Services\MISAndDashboardService;
use Illuminate\Http\Request;

class MISAndDashboardController extends Controller
{

    public function allTeams(Request $request, MISAndDashboardService $MISAndDashboardService)
    {
        \Validator::make($request->all(), ['fiscal_year_id' => 'integer|required'])->validate();

        $all_teams = $MISAndDashboardService->allTeams($request);

        if (isSuccessResponse($all_teams)) {
            $response = responseFormat('success', $all_teams['data']);
        } else {
            $response = responseFormat('error', $all_teams['data']);
        }
        return response()->json($response);
    }

    public function fiscalYearWiseTeams(Request $request, MISAndDashboardService $MISAndDashboardService)
    {

        \Validator::make($request->all(), ['fiscal_year_id' => 'integer|required', 'office_id' => 'integer|required'])->validate();

        $all_teams = $MISAndDashboardService->fiscalYearWiseTeams($request);

        if (isSuccessResponse($all_teams)) {
            $response = responseFormat('success', $all_teams['data']);
        } else {
            $response = responseFormat('error', $all_teams['data']);
        }
        return response()->json($response);
    }

}
