<?php

namespace App\Http\Controllers;

use App\Models\AnnualPlanMain;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\ApOfficeOrder;
use App\Models\AuditVisitCalendarPlanTeam;
use App\Models\AuditVisitCalenderPlanMember;
use App\Models\OpActivity;
use App\Models\XFiscalYear;
use App\Models\XResponsibleOffice;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HiddenSupportFeature extends Controller
{
    use GenericData;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $directorates =  XResponsibleOffice::all();
        $fiscal_years = XFiscalYear::all();

        return view('support_feature.index',compact('directorates','fiscal_years'));
    }

    public function getFiscalYearWiseActivity(Request $request){
        $fiscal_year_id =  $request->fiscal_year_id;
        $activities = OpActivity::where('fiscal_year_id',$fiscal_year_id)->where('activity_type','compliance')->get();
        return view('support_feature.activity_select',compact('activities'));
    }

    public function getAuditPlanData(Request $request){
        $directorate_id = $request->directorate_id;
        $office_db_con_response = $this->switchOffice($directorate_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        $fiscal_year_id = $request->fiscal_year_id;
        $activity_id = $request->activity_id;

        $annual_plan_main =  AnnualPlanMain::where('fiscal_year_id',$fiscal_year_id)
            ->where('activity_type','compliance')
            ->first();

        $audit_plans = ApEntityIndividualAuditPlan::select('id','edit_user_details','edit_time_start')
            ->where('fiscal_year_id',$fiscal_year_id)
            ->where('activity_id',$activity_id)
            ->get();


        $office_orders = ApOfficeOrder::select(DB::raw("CONCAT(COALESCE(`id`,''),'-',COALESCE(`approved_status`,'')) AS plan_and_status"),'audit_plan_id')
            ->where('fiscal_year_id',$fiscal_year_id)
            ->where('approved_status','approved')
            ->orWhere('approved_status','draft')
            ->pluck('plan_and_status','audit_plan_id');

//        dd($office_orders);

        return view('support_feature.load_audit_plan_data',compact('annual_plan_main','audit_plans','office_orders'));
    }

    public function annualPlanApprovalStatus(Request $request){
        $directorate_id = $request->directorate_id;
        $office_db_con_response = $this->switchOffice($directorate_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'msg' => $office_db_con_response];
        }

        $annual_plan_main = AnnualPlanMain::find($request->annual_plan_main_id);
        $annual_plan_main->approval_status = $request->status;
        $annual_plan_main->save();

        return ['status' => 'success', 'msg' => 'Status Update Successfully'];

    }

    public function officeOrderApprovalStatus(Request $request){
        $directorate_id = $request->directorate_id;
        $office_db_con_response = $this->switchOffice($directorate_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'msg' => $office_db_con_response];
        }

        $office_order = ApOfficeOrder::find($request->office_order_id);
        $office_order->approved_status = $request->status;
        $office_order->save();

        return ['status' => 'success', 'msg' => 'Status Update Successfully'];

    }

    public function auditPlanDelete(Request $request){
        $directorate_id = $request->directorate_id;
        $office_db_con_response = $this->switchOffice($directorate_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'msg' => $office_db_con_response];
        }

        $plan = ApEntityIndividualAuditPlan::find($request->plan_id);

        if($request->plan_type == 'update_lock'){
            $plan->edit_employee_id = null;
            $plan->edit_user_details = null;
            $plan->edit_time_start = null;
            $plan->save();
            return ['status' => 'success', 'msg' => 'Remove Update Lock Successfully'];
        }

        if($request->plan_type == 'plan'){
            $plan->delete();
        }

        AuditVisitCalendarPlanTeam::where('audit_plan_id',$request->plan_id)->delete();
        AuditVisitCalenderPlanMember::where('audit_plan_id',$request->plan_id)->delete();

        return ['status' => 'success', 'msg' => 'Delete Successfully'];

    }
}
