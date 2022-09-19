<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AnnualPlanMovement;
use App\Models\AnnualPlanPSR;
use App\Models\PsrMovement;
use Illuminate\Http\Request;
use App\Traits\ApiHeart;
use App\Traits\GenericData;

class ApPSRAnnualPlanService
{
    use GenericData, ApiHeart;

    public function store(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
            $annualPlanPSR = empty($request->psr_plan_id) ? new AnnualPlanPSR() : AnnualPlanPSR::find($request->psr_plan_id);
            $annualPlanPSR->annual_plan_id = $request->annual_plan_id;
            $annualPlanPSR->activity_id = $request->activity_id;
            $annualPlanPSR->fiscal_year_id = $request->fiscal_year_id;
            $annualPlanPSR->status = $request->status;
            $annualPlanPSR->plan_description = $request->plan_description;
            $annualPlanPSR->created_by = $cdesk->officer_id;
            $annualPlanPSR->save();
            return ['status' => 'success', 'data' => $annualPlanPSR->id];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function view(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
        try {
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }
            $psr = AnnualPlanPSR::where('id',$request->psr_plan_id)->first()->toArray();
            return ['status' => 'success', 'data' => $psr];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function update(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_id = $request->office_id ? $request->office_id : $cdesk->office_id;
            $office_db_con_response = $this->switchOffice($office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $annualPlanPSR = AnnualPlanPSR::find($request->psr_id);
            $annualPlanPSR->annual_plan_id = $request->annual_plan_id ? $request->annual_plan_id : $annualPlanPSR->annual_plan_id;
            $annualPlanPSR->activity_id = $request->activity_id ? $request->activity_id : $annualPlanPSR->activity_id;
            $annualPlanPSR->fiscal_year_id = $request->fiscal_year_id ? $request->fiscal_year_id : $annualPlanPSR->fiscal_year_id;
            $annualPlanPSR->status = $request->status ? $request->status : $annualPlanPSR->status;
            $annualPlanPSR->office_approval_status = $request->office_approval_status ? $request->office_approval_status : $annualPlanPSR->office_approval_status;
            $annualPlanPSR->is_sent_cag = $request->is_sent_cag ? $request->is_sent_cag : $annualPlanPSR->is_sent_cag;
            $annualPlanPSR->plan_description = $request->plan_description ? $request->plan_description : $annualPlanPSR->plan_description;
            $annualPlanPSR->created_by = $cdesk->officer_id;
            $annualPlanPSR->modified_by = $cdesk->officer_id;
            $annualPlanPSR->save();

            return ['status' => 'success', 'data' => 'Send Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function sendToOcag(Request $request): array
    {
        try {

            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            AnnualPlan::whereIn('id',$request->annual_plan_ids)
                ->update(['is_sent_cag' => 1, 'status' => 'pending']);

            return ['status' => 'success', 'data' => 'Send Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPsrApprovalList(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($request->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $psr_approval_plan_list =  AnnualPlan::select('id','subject_matter','status')->where('fiscal_year_id',$request->fiscal_year_id)
                ->where('activity_type',$request->activity_type)
                ->where('is_sent_cag',1)
                ->get();

            return ['status' => 'success', 'data' => $psr_approval_plan_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function approvePsrTopic(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($request->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $psr_approval_plan_list =  AnnualPlan::where('id',$request->annual_plan_id)
                ->update(['status' => 'approved']);

            return ['status' => 'success', 'data' => $psr_approval_plan_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function getPsrReportApprovalList(Request $request): array
    {
        try {
            $cdesk = json_decode($request->cdesk, false);
            $office_db_con_response = $this->switchOffice($request->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $psr_approval_plan_list =  AnnualPlanPSR::select('id','annual_plan_id','status')
                ->with('annual_plan:id,subject_matter')
                ->where('fiscal_year_id',$request->fiscal_year_id)
                ->where('is_sent_cag',1)
                ->get();

            return ['status' => 'success', 'data' => $psr_approval_plan_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function sendPsrSenderToReceiver(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        \DB::beginTransaction();

        try {

            foreach ($request->psr_list as $psr){

                $update_data['status'] = 'pending';
                $update_data['is_sent_cag'] = 1;

                if($request->psr_approval_type == 'topic'){
                    AnnualPlan::where('id',$psr)
                        ->update($update_data);
                }else{
                    AnnualPlanPSR::where('annual_plan_id',$psr)
                        ->update($update_data);
                }

                $data = [
                    'fiscal_year_id' => $request->fiscal_year_id,
                    'annual_plan_id' => $psr,
                    'psr_movement_type' => $request->psr_approval_type,
                    'sender_office_id' => $cdesk->office_id,
                    'sender_office_name_en' => $cdesk->office_name_en,
                    'sender_office_name_bn' => $cdesk->office_name_bn,
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
                PsrMovement::create($data);
            }

            \DB::commit();
            $responseData = ['status' => 'success', 'data' => 'Successfully Sent'];
        } catch (\Exception $exception) {
            \DB::rollback();
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function sendPsrReceiverToSender(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($request->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $annualPlanMovement = PsrMovement::where('fiscal_year_id',$request->fiscal_year_id)
                ->where('annual_plan_id',$request->annual_plan_id)
                ->where('sender_office_id',$request->office_id)
                ->where('receiver_office_id',$cdesk->office_id)
                ->where('psr_movement_type',$request->psr_approval_type)
                ->where('status','pending')
                ->latest()
                ->first();

//            return ['status' => 'error', 'data' => $request->all()];

            $data = [
                'fiscal_year_id' => $request->fiscal_year_id,
                'annual_plan_id' => $request->annual_plan_id,
                'psr_movement_type' => $request->psr_approval_type,
                'sender_office_id' => $cdesk->office_id,
                'sender_office_name_en' => $cdesk->office_name_en,
                'sender_office_name_bn' => $cdesk->office_name_bn,
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
                'receiver_office_id' => $annualPlanMovement->sender_office_id,
                'receiver_office_name_en' => $annualPlanMovement->sender_office_name_en,
                'receiver_office_name_bn' => $annualPlanMovement->sender_office_name_bn,
                'receiver_unit_id' => $annualPlanMovement->sender_unit_id,
                'receiver_unit_name_en' => $annualPlanMovement->sender_unit_name_en,
                'receiver_unit_name_bn' => $annualPlanMovement->sender_unit_name_bn,
                'receiver_officer_id' => $annualPlanMovement->sender_officer_id,
                'receiver_name_en' => $annualPlanMovement->sender_name_en,
                'receiver_name_bn' => $annualPlanMovement->sender_name_bn,
                'receiver_designation_id' => $annualPlanMovement->sender_designation_id,
                'receiver_designation_en' => $annualPlanMovement->sender_designation_en,
                'receiver_designation_bn' => $annualPlanMovement->sender_designation_bn,

                'status' => $request->status,
                'comments' => $request->comments
            ];

            PsrMovement::create($data);

            $is_sent_cag = $request->status == 'approved' ? 1 : 0;

            if($request->psr_approval_type == 'topic'){
                AnnualPlan::where('id',$request->annual_plan_id)
                    ->update(
                        [
                            'status' => $request->status,
                            'is_sent_cag' => $is_sent_cag
                        ]
                    );
            }else{
                AnnualPlanPSR::where('annual_plan_id',$request->annual_plan_id)
                    ->update(['status' => $request->status, 'is_sent_cag' => $is_sent_cag]);
            }

            $responseData = ['status' => 'success', 'data' => 'Status Update Successfully'];

        } catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        $this->emptyOfficeDBConnection();
        return $responseData;
    }

    public function getPsrMovementHistories(Request $request): array
    {
        try{
            $annualPlanMovementList = AnnualPlanMovement::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('op_audit_calendar_event_id', $request->op_audit_calendar_event_id)
                ->latest()
                ->get();
            $responseData = ['status' => 'success', 'data' => $annualPlanMovementList];
        }catch (\Exception $exception) {
            $responseData = ['status' => 'error', 'data' => $exception->getMessage()];
        }
        return $responseData;
    }
}
