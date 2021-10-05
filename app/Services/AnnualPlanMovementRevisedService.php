<?php

namespace App\Services;

use App\Models\AnnualPlanMovement;
use App\Models\OpOrganizationYearlyAuditCalendarEvent;
use App\Models\XFiscalYear;
use App\Traits\GenericData;
use Illuminate\Http\Request;

class AnnualPlanMovementRevisedService
{
    use GenericData;

    public function storeApprovalAuthority(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);

        $xFiscalYear = XFiscalYear::where('id',$request->fiscal_year_id)->first();
        try {
            $isExistApprovalAuthority = AnnualPlanMovement::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('op_audit_calendar_event_id', $request->op_audit_calendar_event_id)
                ->where('receiver_officer_id', $request->receiver_officer_id)
                ->exists();

            if ($isExistApprovalAuthority == false){
                $data = [
                    'fiscal_year_id' => $request->fiscal_year_id,
                    'op_audit_calendar_event_id' => $request->op_audit_calendar_event_id,
                    'duration_id' => $xFiscalYear->duration_id,

                    'sender_office_id' => $cdesk->office_id,
                    'sender_office_name_en' => $cdesk->office,
                    'sender_office_name_bn' => $cdesk->office,
                    'sender_unit_id' => $cdesk->office_unit_id,
                    'sender_unit_name_en' => $cdesk->office_unit_en,
                    'sender_unit_name_bn' => $cdesk->office_unit_bn,
                    'sender_officer_id' => $cdesk->officer_id,
                    'sender_name_en' => $cdesk->officer_en,
                    'sender_name_bn' => $cdesk->officer_bn,
                    'sender_designation_id' => $cdesk->designation_id,
                    'sender_designation_en' => $cdesk->designation_en,
                    'sender_designation_bn' => $cdesk->designation_bn,

                    'receiver_type' => $request->receiver_type,
                    'receiver_office_id' => $request->receiver_office_id,
                    'receiver_office_name_en' => $request->receiver_office_name_en,
                    'receiver_office_name_bn' => $request->receiver_office_name_bn,
                    'receiver_unit_id' => $request->receiver_unit_id,
                    'receiver_unit_name_en' => $request->receiver_unit_name_en,
                    'receiver_unit_name_bn' => $request->receiver_unit_name_bn,
                    'receiver_officer_id' => $request->receiver_officer_id,
                    'receiver_name_en' => $request->receiver_name_en,
                    'receiver_name_bn' => $request->receiver_name_bn,
                    'receiver_designation_id' => $request->receiver_designation_id,
                    'receiver_designation_en' => $request->receiver_designation_en,
                    'receiver_designation_bn' => $request->receiver_designation_bn,

                    'status' => $request->status,
                    'comments' => $request->comments
                ];

                AnnualPlanMovement::create($data);

                //update op organization yearly audit calendar event
                OpOrganizationYearlyAuditCalendarEvent::where("id", $request->op_audit_calendar_event_id)
                    ->update(["approval_status" => $request->status]);

                $responseData = ['status' => 'success', 'data' => 'Successfully Saved!'];
            }
            else{
                $responseData = ['status' => 'error', 'data' => 'Data Exist'];
            }
        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }

        return $responseData;
    }

    public function getMovementHistories(Request $request): array
    {
        try{
            $annualPlanMovementList = AnnualPlanMovement::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('op_audit_calendar_event_id', $request->op_audit_calendar_event_id)
                ->get();
            $responseData = ['status' => 'success', 'data' => $annualPlanMovementList];
        }catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $responseData;
    }
}
