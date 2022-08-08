<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApOfficeOrder;
use App\Models\ApOfficeOrderMovement;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalendarPlanTeamUpdate;
use App\Models\AuditVisitCalenderPlanMember;
use App\Traits\GenericData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ApOfficerOrderService
{
    use GenericData;

    public function auditPlanList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);

        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $fiscal_year_id = $request->fiscal_year_id;
            $activity_id = $request->activity_id;

            $query = ApEntityIndividualAuditPlan::query();

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($activity_id, function ($q, $activity_id) {
//                $q->whereHas('office_order', function ($q) use ($activity_id) {
//                    return $q->where('activity_id', $activity_id);
//                });

                return $q->where('activity_id', $activity_id);
            });

            $auditPlanList =  $query->has('audit_teams')
                ->with(['annual_plan.ap_entities','audit_teams','office_order.office_order_movement','office_order_log'])
                ->with('office_order_update.office_order_movement',function($q) use($query) {
                    $query->where('has_update_office_order',1);
                })
                ->where('status','approved')
                ->withCount('audit_team_update')
                ->paginate($request->per_page ?: config('bee_config.per_page_pagination'));

//            if ($request->per_page && $request->page && !$request->all) {
//                $auditPlanList = ApEntityIndividualAuditPlan::has('audit_teams')
//                    ->with(['annual_plan.ap_entities','audit_teams','office_order.office_order_movement'])
//                    ->where('fiscal_year_id', $request->fiscal_year_id)
//                    ->where('status','approved')
//                    ->paginate($request->per_page);
//            }
//            else{
//                $auditPlanList = ApEntityIndividualAuditPlan::has('audit_teams')
//                    ->with(['annual_plan.ap_entities','audit_teams','office_order.office_order_movement'])
//                    ->where('fiscal_year_id', $request->fiscal_year_id)
//                    ->where('status','approved')
//                    ->get();
//            }

            $responseData = ['status' => 'success', 'data' => $auditPlanList];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }


    public function showOfficeOrder(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        $office_db_con_response = $this->switchOffice($office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $officeOrder = ApOfficeOrder::with(['office_order_movement'])->where('id',$request->office_order_id)
                ->where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->first()
                ->toArray();

            $auditTeamAllMembers = AuditVisitCalenderPlanMember::distinct()
                ->select('team_member_name_bn','team_member_name_en','team_member_designation_bn',
                    'team_member_designation_en','team_member_role_bn','team_member_role_en','mobile_no','employee_grade')
                ->where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->orderBy('employee_grade','ASC')
                ->get()
                ->toArray();

            $auditTeamWiseSchedule = AuditVisitCalendarPlanTeam::where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->get()
                ->toArray();

            $officeOrderInfo = [
                'office_order' => $officeOrder,
                'audit_team_members' => $auditTeamAllMembers,
                'audit_team_schedules' => $auditTeamWiseSchedule,
            ];

            $responseData = ['status' => 'success', 'data' => $officeOrderInfo];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function showUpdateOfficeOrder(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $officeOrder = ApOfficeOrder::with(['office_order_movement'])->find($request->office_order_id);

            $auditTeamWiseSchedule = AuditVisitCalendarPlanTeamUpdate::where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->get();

//            $responseData = ['status' => 'success', 'data' => $auditTeamWiseSchedule];
//
//
            $all_schedule_members = [];

            foreach ($auditTeamWiseSchedule as $schedule){
//                return ['status' => 'success', 'data' => json_decode($schedule['team_members'],true)];

                $all_members =  json_decode($schedule['team_members'],true);

                foreach ($all_members['teamLeader'] as  $teamLeader ){
                   $team_leader_temp['team_member_name_bn'] =  $teamLeader['officer_name_bn'];
                   $team_leader_temp['team_member_name_en'] =  $teamLeader['officer_name_en'];
                   $team_leader_temp['team_member_designation_bn'] =  $teamLeader['designation_bn'];
                   $team_leader_temp['team_member_designation_en'] =  $teamLeader['designation_en'];
                   $team_leader_temp['team_member_role_bn'] =  $teamLeader['team_member_role_bn'];
                   $team_leader_temp['team_member_role_en'] =  $teamLeader['team_member_role_en'];
                   $team_leader_temp['mobile_no'] =  $teamLeader['officer_mobile'];
                   $team_leader_temp['email'] =  $teamLeader['officer_email'];
                   $team_leader_temp['employee_grade'] =  $teamLeader['employee_grade'];
                   $all_schedule_members[] = $team_leader_temp;
                }

                if(isset($all_members['subTeamLeader'])){
                    foreach ($all_members['subTeamLeader'] as  $subTeamLeader ){
                        $sub_leader_temp['team_member_name_bn'] =  $subTeamLeader['officer_name_bn'];
                        $sub_leader_temp['team_member_name_en'] =  $subTeamLeader['officer_name_en'];
                        $sub_leader_temp['team_member_designation_bn'] =  $subTeamLeader['designation_bn'];
                        $sub_leader_temp['team_member_designation_en'] =  $subTeamLeader['designation_en'];
                        $sub_leader_temp['team_member_role_bn'] =  $subTeamLeader['team_member_role_bn'];
                        $sub_leader_temp['team_member_role_en'] =  $subTeamLeader['team_member_role_en'];
                        $sub_leader_temp['mobile_no'] =  $subTeamLeader['officer_mobile'];
                        $sub_leader_temp['email'] =  $subTeamLeader['officer_email'];
                        $sub_leader_temp['employee_grade'] =  $subTeamLeader['employee_grade'];
                        $all_schedule_members[] = $sub_leader_temp;
                    }
                }

                foreach ($all_members['member'] as  $member ){
                    $member_temp['team_member_name_bn'] =  $member['officer_name_bn'];
                    $member_temp['team_member_name_en'] =  $member['officer_name_en'];
                    $member_temp['team_member_designation_bn'] =  $member['designation_bn'];
                    $member_temp['team_member_designation_en'] =  $member['designation_en'];
                    $member_temp['team_member_role_bn'] =  $member['team_member_role_bn'];
                    $member_temp['team_member_role_en'] =  $member['team_member_role_en'];
                    $member_temp['mobile_no'] =  $member['officer_mobile'];
                    $member_temp['email'] =  $member['officer_email'];
                    $member_temp['employee_grade'] =  $member['employee_grade'];
                    $all_schedule_members[] = $member_temp;
                }
            }

            $officeOrderInfo = [
                'office_order' => $officeOrder,
                'audit_team_members' => $all_schedule_members,
                'audit_team_schedules' => $auditTeamWiseSchedule,
            ];

            $responseData = ['status' => 'success', 'data' => $officeOrderInfo];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function generateOfficeOrder(Request $request): array
    {

       //return ['status' => 'error', 'data' =>date('Y/m/d',strtotime($request->memorandum_date))];

        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $annualPlan = AnnualPlan::find($request->annual_plan_id);

            //audit plan
            $auditPlan = ApEntityIndividualAuditPlan::find($request->audit_plan_id);
            $auditPlan->has_office_order = 1;
            $auditPlan->has_update_office_order = $auditPlan->has_update_office_order == 2 ? 1 : $auditPlan->has_update_office_order;
            $auditPlan->save();

            $data = [
                'annual_plan_id' => $request->annual_plan_id,
                'schedule_id' => $auditPlan->schedule_id,
                'activity_id' => $auditPlan->activity_id,
                'milestone_id' => $auditPlan->milestone_id,
                'fiscal_year_id' => $auditPlan->fiscal_year_id,
                'audit_plan_id' => $request->audit_plan_id,
                'duration_id' => $annualPlan->activity->duration_id,
                'outcome_id' => $annualPlan->activity->outcome_id,
                'output_id' => $annualPlan->activity->output_id,
                'memorandum_no' => $request->memorandum_no,
                'memorandum_date' => $request->memorandum_date,
                'heading_details' => $request->heading_details,
                'advices' => $request->advices,
                'approved_status' => $request->approved_status,
                'order_cc_list' => $request->order_cc_list,
                'cc_sender_details' => $request->cc_sender_details,
                'draft_officer_id' => $cdesk->officer_id,
                'draft_officer_name_en' => $cdesk->officer_en,
                'draft_officer_name_bn' => $cdesk->officer_bn,
                'draft_designation_id' => $cdesk->designation_id,
                'draft_designation_name_en' => $cdesk->designation_en,
                'draft_designation_name_bn' => $cdesk->designation_bn,
                'draft_office_unit_id' => $cdesk->office_unit_id,
                'draft_office_unit_en' => $cdesk->office_unit_en,
                'draft_office_unit_bn' => $cdesk->office_unit_bn,
                'draft_officer_phone' => $cdesk->phone,
                'draft_officer_email' => $cdesk->email,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];

            ApOfficeOrder::updateOrcreate(['id' => $request->id,'annual_plan_id' => $request->annual_plan_id,
                'audit_plan_id' => $request->audit_plan_id],$data);
            $responseData = ['status' => 'success', 'data' => 'Successfully Office Order Generated!'];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function storeOfficeOrderApprovalAuthority(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $data = [
                'ap_office_order_id' => $request->ap_office_order_id,
                'annual_plan_id' => $request->annual_plan_id,
                'audit_plan_id' => $request->audit_plan_id,
                'office_id' => $request->office_id,
                'unit_id' => $request->unit_id,
                'unit_name_en' => $request->unit_name_en,
                'unit_name_bn' => $request->unit_name_bn,
                'officer_type' => $request->officer_type,
                'employee_id' => $request->employee_id,
                'employee_name_en' => $request->employee_name_en,
                'employee_name_bn' => $request->employee_name_bn,
                'employee_designation_id' => $request->employee_designation_id,
                'employee_designation_en' => $request->employee_designation_en,
                'employee_designation_bn' => $request->employee_designation_bn,
                'officer_phone' => $request->officer_phone,
                'officer_email' => $request->officer_email,
                'received_by' => $request->received_by,
                'sent_by' => $cdesk->officer_id,
                'created_by' => $cdesk->officer_id,
                'modified_by' => $cdesk->officer_id,
            ];

            //ap office order movement
            ApOfficeOrderMovement::updateOrcreate(['ap_office_order_id' => $request->ap_office_order_id,
                'annual_plan_id' => $request->annual_plan_id,
                'audit_plan_id' => $request->audit_plan_id,
                'officer_type' => $request->officer_type
            ],$data);

            //ap office order
            ApOfficeOrder::where('id', $request->ap_office_order_id)->update([
                'approved_status' => 'pending'
            ]);

            \DB::commit();
            $responseData = ['status' => 'success', 'data' => 'Successfully Saved!'];
        } catch (\Exception $exception) {
            \DB::rollback();
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function approveOfficeOrder(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            if($request->has_office_order_update == 1){

                AuditVisitCalendarPlanTeam::where('audit_plan_id',$request->audit_plan_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->delete();

                ApOfficeOrder::where('annual_plan_id',$request->annual_plan_id)
                    ->where('audit_plan_id',$request->audit_plan_id)
                    ->update(['approved_status' => 'log']);

                $team_log =  AuditVisitCalendarPlanTeamUpdate::where('audit_plan_id',$request->audit_plan_id)->get()->makeHidden(['created_at','updated_at'])->toArray();

                AuditVisitCalendarPlanTeam::insert($team_log);

                AuditVisitCalenderPlanMember::where('audit_plan_id', $request->audit_plan_id)->delete();

                $apEntityTeamService =  New ApEntityTeamService();
                foreach ($team_log as $schedule){

                    $team_schedules = json_decode($schedule['team_schedules'], true);

                    $team_deg_schedules[$schedule['leader_designation_id']] = $team_schedules;

                    $apEntityTeamService->saveTeamSchedule($team_deg_schedules,$request->audit_plan_id,$request->annual_plan_id);
                }

                ApEntityIndividualAuditPlan::where('id',$request->audit_plan_id)->update(['has_update_office_order' => 0]);
                AuditVisitCalendarPlanTeamUpdate::where('audit_plan_id',$request->audit_plan_id)->delete();
            }

            $apOfficeOrder = ApOfficeOrder::find($request->ap_office_order_id);
            $apOfficeOrder->approved_status = $request->approved_status;
            $apOfficeOrder->save();

            $responseData = ['status' => 'success', 'data' => 'Approved Successfully'];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function storeOfficeOrderLog(Request $request)
    {
//        return ['status' => 'success', 'data' => $request->cdesk];

        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $folder_name = $cdesk->office_id;
            $file_name = 'office_order_'.$request->office_order_id.'.pdf';

            Storage::disk('public')->put('office_order_log/' . $folder_name . '/' . $file_name, base64_url_decode($request->office_order_pdf_log));

            $file_path = 'storage/office_order_log/' . $folder_name . '/' . $file_name;

            ApOfficeOrder::where('id',$request->office_order_id)->update(['log_path' => $file_path]);

//            $apOfficeOrder = ApOfficeOrder::find($request->ap_office_order_id);
//            $apOfficeOrder->approved_status = $request->approved_status;
//            $apOfficeOrder->save();

            $responseData = ['status' => 'success', 'data' => $file_path];
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;

    }
}
