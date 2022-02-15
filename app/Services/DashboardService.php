<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\AcQuery;
use App\Traits\GenericData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardService
{
    use GenericData;

    public function getTotalDailyQueryAndMemo(Request $request): array
    {
        $connectOfficeDB = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($connectOfficeDB)) {
            return ['status' => 'error', 'data' => $connectOfficeDB];
        }
        try {
            //for total query
            $acQuery = AcQuery::where('fiscal_year_id',$request->fiscal_year_id);
            if (!empty($request->cost_center_id)){
                $acQuery = $acQuery->where('cost_center_id',$request->cost_center_id);
            }
            if (!empty($request->team_id)){
                $acQuery = $acQuery->where('team_id',$request->team_id);
            }
            $getTotalQuery = $acQuery->whereDate('created_at', date('Y-m-d'))->count();

            //for total memo
            $acMemo = AcMemo::where('fiscal_year_id',$request->fiscal_year_id);
            if (!empty($request->cost_center_id)){
                $acMemo = $acMemo->where('cost_center_id',$request->cost_center_id);
            }
            if (!empty($request->team_id)){
                $acMemo = $acMemo->where('team_id',$request->team_id);
            }
            $getTotalMemo = $acMemo->whereDate('created_at', date('Y-m-d'))->count();

            $data['total_query'] = $getTotalQuery;
            $data['total_memo'] = $getTotalMemo;
            return ['status' => 'success', 'data' => $data];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getTotalWeeklyQueryAndMemo(Request $request): array
    {
        //$cdesk = json_decode($request->cdesk, false);
        $connectOfficeDB = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($connectOfficeDB)) {
            return ['status' => 'error', 'data' => $connectOfficeDB];
        }
        try {
            $toDate = date('Y-m-d');
            $fromDate = date('Y-m-d', strtotime($toDate . ' -6 day'));

            //for total query
            $acQuery = AcQuery::where('fiscal_year_id',$request->fiscal_year_id);
            if (!empty($request->cost_center_id)){
                $acQuery = $acQuery->where('cost_center_id',$request->cost_center_id);
            }
            if (!empty($request->team_id)){
                $acQuery = $acQuery->where('team_id',$request->team_id);
            }
            $getTotalQuery = $acQuery->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate)->count();

            //for total memo
            $acMemo = AcMemo::where('fiscal_year_id',$request->fiscal_year_id);
            if (!empty($request->cost_center_id)){
                $acMemo = $acMemo->where('cost_center_id',$request->cost_center_id);
            }
            if (!empty($request->team_id)){
                $acMemo = $acMemo->where('team_id',$request->team_id);
            }
            $getTotalMemo = $acMemo->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate)->count();

            $data['total_query'] = $getTotalQuery;
            $data['total_memo'] = $getTotalMemo;
            return ['status' => 'success', 'data' => $data];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
