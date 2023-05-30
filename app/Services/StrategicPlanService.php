<?php

namespace App\Services;
use App\Models\StrategicPlan;
use App\Models\StrategicPlanLocation;
use Illuminate\Http\Request;
use DB;

class StrategicPlanService
{

    public function list(Request $request): array
    {
        try {
            $strategic_plan_list = StrategicPlan::get();
            return ['status' => 'success', 'data' => $strategic_plan_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
    public function delete(Request $request): array
    {
        try {
            StrategicPlan::where('x_sp_duration_id',$request->strategic_plan_id)->delete();
            StrategicPlanLocation::where('strategic_plan_id',$request->strategic_plan_id)->delete();

            return ['status' => 'success', 'data' => 'Strategic Plan Delete Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function store(Request $request): array
    {
        DB::beginTransaction();

        try {

            $strategic_plan = new StrategicPlan();
            $strategic_plan->x_sp_duration_id = $request->strategic_duration_id;
            $strategic_plan->strategic_plan_year = $request->strategic_plan_year;
            $strategic_plan->created_by = 1;
            $strategic_plan->save();

            foreach($request->strategic_info as $strategic){
                StrategicPlanLocation::insert($strategic);
            }

            DB::commit();
            return ['status' => 'success', 'data' => 'Save Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
    public function update(Request $request): array
    {

        try {
            foreach($request->strategic_info as $strategic){
                StrategicPlanLocation::upsert($strategic,['id']);
            }
            return ['status' => 'success', 'data' => 'Update Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
    public function deleteLocation(Request $request): array
    {

        try {
            StrategicPlanLocation::where('id',$request->location_id)->delete();
            return ['status' => 'success', 'data' => 'Delete Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getIndividualStrategicPlan(Request $request): array
    {
        try {
            // return ['status' => 'success', 'data' => $request->all()];
            
            $project_query = StrategicPlanLocation::where('strategic_plan_id',$request->strategic_plan_id);
                if ($request->strategic_plan_year) {
                    $project_query->where('strategic_plan_year',$request->strategic_plan_year);
                }
            $year_wise_location_project = $project_query->whereNotNull('project_id')
                ->get()
                ->toArray();

            $function_query = StrategicPlanLocation::where('strategic_plan_id',$request->strategic_plan_id);
                if ($request->strategic_plan_year) {
                    $function_query->where('strategic_plan_year',$request->strategic_plan_year);
                }
            $year_wise_location_function = $function_query->whereNotNull('function_id')
                ->get()
                ->toArray();

            $cost_centers_query = StrategicPlanLocation::where('strategic_plan_id',$request->strategic_plan_id);
                if ($request->strategic_plan_year) {
                    $function_query->where('strategic_plan_year',$request->strategic_plan_year);
                }
            $year_wise_location_cost_centers = $cost_centers_query->whereNotNull('cost_center_id')
                ->get()
                ->toArray();

            if ($request->scope == 'download') {
                $data = array_merge($year_wise_location_project, $year_wise_location_function, $year_wise_location_cost_centers);
                $groupedData = collect($data)->groupBy('strategic_plan_year');
                $strategic_plan_list = [];
                foreach ($groupedData as $key => $strategic_plan) {
                    $projects = [];
                    $functions = [];
                    foreach ($strategic_plan as $plan) {
                        if ($plan['project_id']) {
                            $projects[] = $plan;
                        } elseif ($plan['function_id']) {
                            $functions[] = $plan;
                        }
                    }
                    $strategic_plan_list[$key]['projects'] = $projects;
                    $strategic_plan_list[$key]['functions'] = $functions;
                }

                return ['status' => 'success', 'data' => $strategic_plan_list];
            } else {
                $data['project_list'] = $year_wise_location_project;
                $data['function_list'] = $year_wise_location_function;
                $data['cost_centers'] = $year_wise_location_cost_centers;

                return ['status' => 'success', 'data' => $data];
            }
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getIndividualStrategicPlanYear(Request $request): array
    {
        try {

            $year_list = StrategicPlanLocation::select('strategic_plan_id','strategic_plan_year')->distinct('strategic_plan_year')
                ->get();
            return ['status' => 'success', 'data' => $year_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

}
