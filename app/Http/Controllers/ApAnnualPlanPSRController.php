<?php

namespace App\Http\Controllers;
use App\Services\ApPSRAnnualPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApAnnualPlanPSRController extends Controller
{
    public function store(Request $request, ApPSRAnnualPlanService $apEntityTeamService): \Illuminate\Http\JsonResponse
    {
        $psrResponse = $apEntityTeamService->store($request);

        if (isSuccessResponse($psrResponse)) {
            $response = responseFormat('success', $psrResponse['data']);
        } else {
            $response = responseFormat('error', $psrResponse['data']);
        }
        return response()->json($response);
    }
    public function view(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        $edit_plan = $ApPSRAnnualPlanService->view($request);

        if (isSuccessResponse($edit_plan)) {
            $response = responseFormat('success', $edit_plan['data']);
        } else {
            $response = responseFormat('error', $edit_plan['data']);
        }
        return response()->json($response);
    }

    public function update(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        $psr_plan = $ApPSRAnnualPlanService->update($request);

        if (isSuccessResponse($psr_plan)) {
            $response = responseFormat('success', $psr_plan['data']);
        } else {
            $response = responseFormat('error', $psr_plan['data']);
        }
        return response()->json($response);
    }

    public function sendToOcag(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        $psr_plan = $ApPSRAnnualPlanService->sendToOcag($request);

        if (isSuccessResponse($psr_plan)) {
            $response = responseFormat('success', $psr_plan['data']);
        } else {
            $response = responseFormat('error', $psr_plan['data']);
        }
        return response()->json($response);

    }

    public function getPsrApprovalList(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        $psr_plan = $ApPSRAnnualPlanService->getPsrApprovalList($request);

        if (isSuccessResponse($psr_plan)) {
            $response = responseFormat('success', $psr_plan['data']);
        } else {
            $response = responseFormat('error', $psr_plan['data']);
        }
        return response()->json($response);

    }

    public function approvePsrTopic(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        $psr_plan = $ApPSRAnnualPlanService->approvePsrTopic($request);

        if (isSuccessResponse($psr_plan)) {
            $response = responseFormat('success', $psr_plan['data']);
        } else {
            $response = responseFormat('error', $psr_plan['data']);
        }
        return response()->json($response);

    }

    public function getPsrReportApprovalList(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        $psr_plan = $ApPSRAnnualPlanService->getPsrReportApprovalList($request);

        if (isSuccessResponse($psr_plan)) {
            $response = responseFormat('success', $psr_plan['data']);
        } else {
            $response = responseFormat('error', $psr_plan['data']);
        }
        return response()->json($response);

    }

    public function sendPsrSenderToReceiver(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'psr_approval_type' => 'required|string',
            'fiscal_year_id' => 'required|integer',
            'receiver_type' => 'required',
            'receiver_office_id' => 'required',
            'receiver_office_name_en' => 'required',
            'receiver_office_name_bn' => 'required',
            'receiver_unit_id' => 'required',
            'receiver_unit_name_en' => 'required',
            'receiver_unit_name_bn' => 'required',
            'receiver_officer_id' => 'required',
            'receiver_name_en' => 'required',
            'receiver_name_bn' => 'required',
            'receiver_designation_id' => 'required',
            'receiver_designation_en' => 'required',
            'receiver_designation_bn' => 'required',
            'cdesk' => 'required|json',
        ])->validate();

        $responseStore = $ApPSRAnnualPlanService->sendPsrSenderToReceiver($request);

        if (isSuccessResponse($responseStore)) {
            $response = responseFormat('success', $responseStore['data']);
        } else {
            $response = responseFormat('error', $responseStore['data']);
        }

        return response()->json($response);
    }

    public function sendPsrReceiverToSender(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {

        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'office_id' => 'required|integer',
            'psr_approval_type' => 'required|string',
            'receiver_type' => 'required',
            'status' => 'required',
            'cdesk' => 'required|json',
        ])->validate();

        $responseStore = $ApPSRAnnualPlanService->sendPsrReceiverToSender($request);
//        dd($responseStore);
        if (isSuccessResponse($responseStore)) {
            $response = responseFormat('success', $responseStore['data']);
        } else {
            $response = responseFormat('error', $responseStore['data']);
        }
        return response()->json($response);
    }

    public function getPsrMovementHistories(Request $request, ApPSRAnnualPlanService $ApPSRAnnualPlanService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'op_audit_calendar_event_id' => 'required|integer',
        ])->validate();

        $responseData = $ApPSRAnnualPlanService->getPsrMovementHistories($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }
        return response()->json($response);
    }
}
