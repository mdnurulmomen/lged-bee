<?php

namespace App\Services;

use App\Models\AcMemo;
use App\Models\AcQuery;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class DashboardService
{
    use GenericData;

    public function getTotalQueryAndMemoReport(Request $request): array
    {
        $connectOfficeDB = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($connectOfficeDB)) {
            return ['status' => 'error', 'data' => $connectOfficeDB];
        }
        try {
            $toDate = date('Y-m-d');
            $fromDate = date('Y-m-d', strtotime($toDate . ' -6 day'));

            //for total query
            $acQuery = AcQuery::where('fiscal_year_id', $request->fiscal_year_id);
            if (!empty($request->entity_id)) {
                $acQuery = $acQuery->where('entity_office_id', $request->entity_id);
            }
            if (!empty($request->cost_center_id)) {
                $acQuery = $acQuery->where('cost_center_id', $request->cost_center_id);
            }
            if (!empty($request->team_id)) {
                $acQuery = $acQuery->where('team_id', $request->team_id);
            }
            if (!empty($request->activity_id)) {
                $acQuery = $acQuery->where('activity_id', $request->activity_id);
            }

            if ($request->scope_report_type == 'daily') {
                $getTotalQuery = $acQuery->whereDate('created_at', $toDate)->count();
            } elseif ($request->scope_report_type == 'weekly') {
                $getTotalQuery = $acQuery->whereDate('created_at', '>=', $fromDate)
                    ->whereDate('created_at', '<=', $toDate)->count();
            } elseif ($request->scope_report_type == 'yearly') {
                $getTotalQuery = $acQuery->count();
            } else {
                $getTotalQuery = $acQuery->whereDate('created_at', $toDate)->count();
            }


            //for total memo
            $acMemo = AcMemo::where('fiscal_year_id', $request->fiscal_year_id);
            if (!empty($request->entity_id)) {
                $acMemo = $acMemo->where('parent_office_id', $request->entity_id);
            }
            if (!empty($request->cost_center_id)) {
                $acMemo = $acMemo->where('cost_center_id', $request->cost_center_id);
            }
            if (!empty($request->team_id)) {
                $acMemo = $acMemo->where('team_id', $request->team_id);
            }
            if (!empty($request->activity_id)) {
                $activity_id = $request->activity_id;
                $acMemo = $acMemo->whereHas('audit_plan', function ($q) use ($activity_id) {
                        return $q->where('activity_id', $activity_id);
                    });
            }

            if ($request->scope_report_type == 'daily') {
                $getTotalMemo = $acMemo->whereDate('created_at', date('Y-m-d'))->count();
            } elseif ($request->scope_report_type == 'weekly') {
                $getTotalMemo = $acMemo->whereDate('created_at', '>=', $fromDate)
                    ->whereDate('created_at', '<=', $toDate)->count();
            } elseif ($request->scope_report_type == 'yearly') {
                $getTotalMemo = $acMemo->count();
            } else {
                $getTotalMemo = $acMemo->whereDate('created_at', date('Y-m-d'))->count();
            }
            $data['total_query'] = $getTotalQuery;
            $data['total_memo'] = $getTotalMemo;
            return ['status' => 'success', 'data' => $data];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
