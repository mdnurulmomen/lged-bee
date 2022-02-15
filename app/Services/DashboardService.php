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
            $data['total_query'] = AcQuery::whereDate('created_at', date('Y-m-d'))->count();
            $data['total_memo'] = AcMemo::whereDate('created_at', date('Y-m-d'))->count();
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

            $data['total_query'] = AcQuery::where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate)->count();

            $data['total_memo'] = AcMemo::where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate)->count();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
