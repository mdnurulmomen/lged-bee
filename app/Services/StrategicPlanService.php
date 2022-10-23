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

    public function getIndividualStrategicPlan(Request $request): array
    {
        try {

            $year_wise_location_project = StrategicPlanLocation::where('strategic_plan_year',$request->strategic_plan_year)
                ->whereNotNull('project_id')
                ->get();

            $year_wise_location_function = StrategicPlanLocation::where('strategic_plan_year',$request->strategic_plan_year)
                ->whereNotNull('function_id')
                ->get();

            $data['project_list'] = $year_wise_location_project;
            $data['function_list'] = $year_wise_location_function;

            return ['status' => 'success', 'data' => $data];
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
