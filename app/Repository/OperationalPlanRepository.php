<?php

namespace App\Repository;

use App\Models\OpActivity;
use App\Models\XStrategicPlanOutcome;
use App\Repository\Contracts\OperationalPlanInterface;
use Illuminate\Http\Request;

class OperationalPlanRepository implements OperationalPlanInterface
{
    public function __construct(OpActivity $op)
    {
        $this->op = $op;
    }

    public function OperationalPlan(Request $request): array
    {
        $data = [];
        $outcomes = XStrategicPlanOutcome::with('plan_output')->get();
        foreach ($outcomes as $outcome) {
            $outputData = [];
            foreach ($outcome->plan_output as $output) {
                $activities = $this->op
                    ->where('fiscal_year_id', $request->fiscal_year_id)
                    ->where('outcome_id', $outcome->id)
                    ->where('output_id', $output->id)
                    ->with(['milestones', 'calendar_activity', 'responsibles'])
                    ->get();

                foreach ($activities as &$activity) {
                    $assigned_staff = 0;
                    foreach ($activity['responsibles'] as $responsible) {
                        $assigned_staff = $assigned_staff + $responsible['assigned_staffs'];
                    }
                    $activity['assigned_staffs'] = $assigned_staff;
                }

                if (count($activities)) {
                    $outputData[] = [
                        'id' => $output->id,
                        'output' => $output->output_no,
                        'output_title' => $output->output_title_en,
                        'output_remarks' => $output->remarks,
                        'activities' => $activities,
                    ];
                }
            }
            if (count($outputData)) {
                $data[] = [
                    'outcome_id' => $outcome->id,
                    'outcome' => $outcome->outcome_title_en,
                    'outcome_remarks' => $outcome->remarks,
                    'output' => $outputData,
                ];
            }
        }
        return $data;
    }

    public function OperationalDetail(Request $request): array
    {
        $tree = $this->op->with(['children'])->get();
        $directorates = $this->op
            ->with(['responsibles'])
            ->where('fiscal_year_id', $request->fiscal_year_id)
            ->get();
        return [
            'tree' => $tree,
            'directorates' => $directorates,
        ];
    }
}
