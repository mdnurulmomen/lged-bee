<?php

namespace App\Repository;

use App\Models\ApEntityIndividualAuditPlan;
use App\Models\OpActivity;
use App\Models\OpActivityMilestone;
use App\Models\XFiscalYear;
use App\Models\XStrategicPlanOutcome;
use App\Repository\Contracts\OpActivityInterface;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class OpActivityRepository implements OpActivityInterface
{
    use GenericData;

    public function __construct(OpActivity $opActivity)
    {
        $this->opActivity = $opActivity;
    }

    public function allActivities(Request $request): array
    {
        $data = [];
        if ($request->per_page && $request->page && !$request->all) {
            $opActivities = $this->opActivity->withCount('milestones')->where('is_activity', 1)->paginate($request->per_page);
        } else {
            $opActivities = $this->opActivity->withCount('milestones')->where('is_activity', 1)->get();
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

    public function findActivities(Request $request): array
    {
        $output_id = $request->output_id;
        $outcome_id = $request->outcome_id;
        $fiscal_year_id = $request->fiscal_year_id;

        $outcomes = XStrategicPlanOutcome::query();

        if (!empty($outcome_id)) {
            $outcomes->where('id', $outcome_id);
        }

        if (!empty($output_id) || !empty($fiscal_year_id)) {
            $outcomes->with(['plan_output' => function ($q) use ($output_id, $fiscal_year_id) {
                if (!empty($output_id)) {
                    $q->where('id', $output_id);
                }
                if (!empty($fiscal_year_id)) {
                    $q->with(['activities' => function ($q) use ($fiscal_year_id) {
                        $q->where('activity_parent_id', 0);
                        $q->where('fiscal_year_id', $fiscal_year_id);
                    }, 'activities.children']);
                } else {
                    $q->with(['activities.children' => function ($q) {
                        $q->where('activity_parent_id', 0);
                    }]);
                }
            }]);
        } else {
            $outcomes->with(['plan_output.activities' => function ($q) {
                $q->where('activity_parent_id', 0);
                $q->with(['children']);
            }]);
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

    public function showActivitiesByFiscalYear(Request $request): array
    {
        $fiscal_year_id = $request->fiscal_year_id;

        $outcomes = XStrategicPlanOutcome::query();

        $outcomes->with(['plan_output' => function ($q) use ($fiscal_year_id) {
            if (!empty($fiscal_year_id)) {
                $q->with(['activities' => function ($q) use ($fiscal_year_id) {
                    $q->where('activity_parent_id', 0);
                    $q->where('fiscal_year_id', $fiscal_year_id);
                }, 'activities.children.milestones', 'activities.milestones']);
            } else {
                $q->with(['activities.children.milestones' => function ($q) {
                    $q->where('activity_parent_id', 0);
                }, 'activities.milestones']);
            }
        }]);


        $activities['data'] = $outcomes->get();
        $activities['fiscal_year_id'] = $fiscal_year_id;

        if (!empty($activities)) {
            $response = responseFormat('success', $activities);
        } else {
            $response = responseFormat('error', 'Not Found');
        }

        return $response;
    }

    public function showActivityMilestones(Request $request)
    {
        $activity_id = $request->activity_id;
        $milestones = OpActivityMilestone::select('id', 'title_en', 'title_bn')->where('activity_id', $activity_id)->with('milestone_calendar')->get();

        return $milestones;
    }

    public function getAllActivity(Request $request): array
    {
        try {
            $activity_list = OpActivity::where('fiscal_year_id', $request->fiscal_year_id)->get();
            return ['status' => 'success', 'data' => $activity_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getActivityWiseAuditPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
//        return ['status' => 'success', 'data' => $activity_plan_list];
        try {
            $activity_plan_list = ApEntityIndividualAuditPlan::with('ap_entities:id,annual_plan_id,ministry_id,entity_id,entity_name_bn,entity_name_en')->select('id','annual_plan_id')->where('fiscal_year_id', $request->fiscal_year_id)->where('activity_id', $request->activity_id)->get();
            return ['status' => 'success', 'data' => $activity_plan_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeActivity($validated_data): array
    {
        try {
            if ($validated_data['activity_parent_id'] && $validated_data['activity_parent_id'] > 0) {
                $validated_data['is_parent'] = 0;
            }
            $validated_data['duration_id'] = $this->durationIdFromFiscalYear($validated_data['fiscal_year_id']);
            OpActivity::create($validated_data);
            $response = responseFormat('success', 'Successfully Created!', ['code' => 200]);
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }
        return $response;
    }
}
