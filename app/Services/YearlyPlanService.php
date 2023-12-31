<?php

namespace App\Services;
use App\Models\YearlyPlan;
use App\Models\YearlyPlanLocation;
use Illuminate\Http\Request;
use DB;

class YearlyPlanService
{

    public function list(Request $request): array
    {
        try {
            $list = YearlyPlan::withCount('has_yearly_plan')->get();
            return ['status' => 'success', 'data' => $list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function store(Request $request): array
    {
        DB::beginTransaction();

        try {

            $strategic_plan = new YearlyPlan();
            $strategic_plan->strategic_plan_id = $request->strategic_plan_id;
            $strategic_plan->strategic_plan_year = $request->strategic_plan_year;
            $strategic_plan->created_by = 1;
            $strategic_plan->save();

            foreach($request->strategic_info as $strategic){
                YearlyPlanLocation::insert($strategic);
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

            $strategic_plan = YearlyPlan::find($request->yearly_plan_id);
            $strategic_plan->strategic_plan_id = $request->strategic_plan_id;
            $strategic_plan->strategic_plan_year = $request->strategic_plan_year;
            $strategic_plan->created_by = 1;
            $strategic_plan->save();

            foreach($request->strategic_info as $strategic){
                $location_id = (int) $strategic['location_id'];
                YearlyPlanLocation::updateOrCreate(
                    ['id' => $location_id],$strategic);
            }
            return ['status' => 'success', 'data' => 'Update Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
    public function deleteYearlyPlan(Request $request): array
    {

        try {
               $year_wise_id =  YearlyPlanLocation::where('strategic_plan_year',$request->strategic_plan_year)->pluck('id');
               YearlyPlanLocation::whereIn('id',$year_wise_id)->delete();

               YearlyPlan::where('id',$request->yearly_plan_id)->delete();
            return ['status' => 'success', 'data' => 'Delete Successfully'];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
    public function yearlyPlanLocationDelete(Request $request): array
    {

        try {
                YearlyPlanLocation::where('id',$request->yearly_plan_locations_id)->delete();
            return ['status' => 'success', 'data' => 'Save Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
    public function getIndividualYearlyPlan(Request $request): array
    {
        try {

            $year_wise_location_project = YearlyPlanLocation::with('audit_plan.milestones')
            ->with('yearly_plan:id,strategic_plan_id')
            ->where('strategic_plan_year',$request->strategic_plan_year)
            ->whereNotNull('project_id')
            ->get();

            $year_wise_location_function = YearlyPlanLocation::with('audit_plan.milestones')
            ->with('yearly_plan:id,strategic_plan_id')
            ->where('strategic_plan_year',$request->strategic_plan_year)
            ->whereNotNull('function_id')
            ->get();


            $data['project_list'] = $year_wise_location_project;
            $data['function_list'] = $year_wise_location_function;
            $data['cost_centers'] = [];

            return ['status' => 'success', 'data' => $data];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getIndividualYearlyPlanYear(Request $request): array
    {
        try {

            $year_list = YearlyPlanLocation::select('strategic_plan_id','strategic_plan_year')
            ->with('yearly_plan:id,strategic_plan_id')
            ->distinct('strategic_plan_year')
            ->get();

            return ['status' => 'success', 'data' => $year_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

}
