<?php

namespace App\Services;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApMilestone;
use App\Models\PlanWorkPaper;
use App\Models\EngagementLetter;
use Illuminate\Http\Request;
use DB;

class IndividualPlanService
{
    public function getAllAuditPlans()
    {
        try {

            $auditPlans = ApEntityIndividualAuditPlan::with(['yearlyPlanLocation'])->get();
            return ['status' => 'success', 'data' => $auditPlans];

        }
        catch (\Exception $e) {

            return ['status' => 'error', 'data' => $e->getMessage()];

        }
    }

    public function getAllWorkPapers(Request $request)
    {
        // return ['status' => 'success', 'data' => $request->audit_plan_id];

        try {

            $auditPlan = PlanWorkPaper::where('audit_plan_id',$request->audit_plan_id)->get();

            return ['status' => 'success', 'data' => $auditPlan];

        }
        catch (\Exception $e) {

            return ['status' => 'error', 'data' => $e->getMessage()];

        }
    }

    public function uploadWorkPapers(Request $request)
    {
        // return ['status' => 'success', 'data' => $request->audit_plan_id];

        DB::beginTransaction();

        try {

            $auditPlan = ApEntityIndividualAuditPlan::find($request->audit_plan_id);

            if(is_file($request['attachment'])) {

                // File Object
                $file = $request['attachment'];

                // File extension
                $extension = $file->getClientOriginalExtension();

                $filename = ($auditPlan->yearlyPlanLocation->id.'-'.$auditPlan->workPapers->count()).".".$extension;

                // File upload location
                $location = 'public/audit-plan/work-papers';

                // Upload file
                $file->storeAs($location, $filename);

                // File path
                $filepath = ('storage/audit-plan/work-papers/'.$filename);
            }

            $auditPlan->workPapers()->create([
                'title_en' => $request->title_en,
                'title_bn' => $request->title_bn,
                'attachment' => $filepath,
                'created_by' => $request->created_by,
                'updated_by' => $request->updated_by,
            ]);

            DB::commit();

            return ['status' => 'success', 'data' => 'Successfully uploaded'];

        }
        catch (\Exception $e) {

            return ['status' => 'error', 'data' => $e->getMessage()];

        }
    }

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
                'audit_type' => $request->audit_type,
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

    public function engagementLetterStore(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $letter_data = [
                'audit_plan_id' => $request->audit_plan_id,
                'letter_to' => $request->letter_to,
                'letter_from' => $cdesk->officer_en,
                'subject' => $request->subject,
                'body' => $request->body,
                'others' => $request->others,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];

           $engagement_letter =  EngagementLetter::insert($letter_data);

            return ['status' => 'success', 'data' => 'Engagement Letter Created successfully'];

        } catch (\Exception $e) {
           return ['status' => 'error', 'data' => $e->getMessage()];
        }

    }
}
