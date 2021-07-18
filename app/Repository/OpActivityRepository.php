<?php

namespace App\Repository;

use App\Models\OpActivity;
use App\Models\XFiscalYear;
use App\Models\XStrategicPlanOutcome;
use App\Repository\Contracts\OpActivityInterface;
use Illuminate\Http\Request;

class OpActivityRepository implements OpActivityInterface
{
    public function __construct(OpActivity $opActivity)
    {
        $this->opActivity = $opActivity;
    }

    public function allActivities(Request $request)
    {
        $data = [];
        if ($request->per_page && $request->page && !$request->all) {
            $opActivities = $this->opActivity->withCount('milestones')->paginate($request->per_page);
        } else {
            $opActivities = $this->opActivity->withCount('milestones')->get();
        }
        $opActivitiesByFiscalYear = $opActivities->groupBy('fiscal_year_id')->toArray();

        foreach ($opActivitiesByFiscalYear as $fiscal_year_id => $opActivities) {
            $out = array();
            foreach ($opActivities as $opActivityKey => $opActivity) {
                foreach ($opActivity as $key2 => $value2) {
                    $index = $key2 . '-' . $value2;
                    if (array_key_exists($index, $out)) {
                        $out[$index]++;
                    } else {
                        $out[$index] = 1;
                    }
                }
            }
            $outcome_count = count(preg_grep('/^outcome_id-[\d]*/', array_keys($out)));
            $output_count = count(preg_grep('/^output_id-[\d]*/', array_keys($out)));
            $milestones = preg_grep('/^milestones_count-[\d]*/', array_keys($out));
            $milestone_count = 0;
            foreach ($milestones as $m_key => $milestone) {
                $milestone_count += $out[$milestone] * substr($milestone, strpos($milestone, "-") + 1);
            }
            $fiscal_year = XFiscalYear::select('description')->where('id', $fiscal_year_id)->first()->description;
            $data[] = [
                'fiscal_year_id' => $fiscal_year_id,
                'fiscal_year' => $fiscal_year,
                'outcome_count' => $outcome_count,
                'output_count' => $output_count,
                'activity_count' => count($opActivities),
                'milestone_count' => $milestone_count,
            ];
        }

        return $data;
    }

    public function findActivities(Request $request)
    {
        $output_id = $request->output_id;
        $outcome_id = $request->outcome_id;
        $fiscal_year_id = $request->fiscal_year_id;

        $outcomes = XStrategicPlanOutcome::query();

        if (!empty($outcome_id)) {
            $outcomes->where('id', $outcome_id);
        }

        if (!empty($output_id)) {
            $outcomes->with(['plan_output' => function ($q) use ($output_id, $fiscal_year_id) {
                $q->where('id', $output_id);
                if (!empty($fiscal_year_id)) {
                    $q->with(['activities' => function ($q) use ($fiscal_year_id) {
                        $q->where('fiscal_year_id', $fiscal_year_id);
                    }]);
                } else {
                    $q->with('activities');
                }
            }]);
        } else {
            $outcomes->with('plan_output.activities');
        }

        $activities['data'] = $outcomes->get();
        $activities['fiscal_year_id'] = $fiscal_year_id;

        if (!empty($activities)) {
            $response = responseFormat('success', $activities);
        } else {
            $response = responseFormat('error', 'Not Found');
        }

        return $response;
    }
}
