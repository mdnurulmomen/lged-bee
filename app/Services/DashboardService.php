<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\Query;
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
            $data['total_query'] = Query::whereDate('created_at', Carbon::today())->count();
            $data['total_memo'] = AcMemo::whereDate('created_at', Carbon::today())->count();
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
            $data['total_query'] = Query::whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::today()])->count();
            $data['total_memo'] = AcMemo::whereBetween('created_at', [Carbon::now()->subDays(7),Carbon::today()])->count();
            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
