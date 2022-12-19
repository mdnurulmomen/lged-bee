<?php

namespace App\Services;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApMilestone;
use Illuminate\Http\Request;
use DB;

class IndividualPlanService
{
    public function auditPlanInfo(Request $request): array
    {
        try {

            $audit_plan_info = ApEntityIndividualAuditPlan::with('milestones')->where('id',$request->audit_plan_id)->first();
            return ['status' => 'success', 'data' => $audit_plan_info];

        } catch (\Exception $e) {
            return ['status' => 'error', 'data' => $e->getMessage()];
        }
    }

    public function store(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $plan_data = [
                'scope' => $request->scope,
                'objective' => $request->objective,
                'plan_no' => 0,
                'yearly_plan_id' => $request->yearly_plan_id,
                'yearly_plan_location_id' => $request->yearly_plan_location_id,
                'has_office_order' => 0,
                'has_update_office_order' => 0,
                'draft_unit_id' => $cdesk->office_unit_id,
                'draft_unit_name_en' => $cdesk->office_unit_en,
                'draft_unit_name_bn' => $cdesk->office_unit_bn,
                'draft_designation_id' => $cdesk->designation_id,
                'draft_designation_name_en' => $cdesk->designation_en,
                'draft_designation_name_bn' => $cdesk->designation_bn,
                'draft_officer_id' => $cdesk->officer_id,
                'draft_officer_name_en' => $cdesk->officer_en,
                'draft_officer_name_bn' => $cdesk->officer_bn,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];

           $audit_plan_id =  ApEntityIndividualAuditPlan::updateOrCreate(
                ['id' => $request->id],
                $plan_data)['id'];


           $milestone_list = collect($request->milestone_list);


            $milestone_list = $milestone_list->map(function ($item) use ($audit_plan_id){
                $item['audit_plan_id'] = $audit_plan_id;
                $item['created_at'] = date('Y-m-d H:i:s');
                $item['updated_at'] = date('Y-m-d H:i:s');
                return $item;
            });


            if($request->id){
                ApMilestone::where('audit_plan_id',$request->id)->delete();
            }
            ApMilestone::insert($milestone_list->toArray());

            return ['status' => 'success', 'data' => 'Plan save successfully'];

        } catch (\Exception $e) {
           return ['status' => 'error', 'data' => $e->getMessage()];
        }


    }
}
