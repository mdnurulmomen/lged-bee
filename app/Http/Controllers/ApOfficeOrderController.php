<?php

namespace App\Http\Controllers;

use App\Services\ApOfficerOrderService;
use App\Services\MISAndDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApOfficeOrderController extends Controller
{
    public function auditPlanList(Request $request, ApOfficerOrderService $apOfficerOrderService): \Illuminate\Http\JsonResponse
    {
        $auditPlanListResponse = $apOfficerOrderService->auditPlanList($request);

        if (isSuccessResponse($auditPlanListResponse)) {
            $response = responseFormat('success', $auditPlanListResponse['data']);
        } else {
            $response = responseFormat('error', $auditPlanListResponse['data']);
        }

        return response()->json($response);

    }

    public function showOfficeOrder(Request $request, ApOfficerOrderService $apOfficerOrderService): \Illuminate\Http\JsonResponse
    {
        $showOfficeOrderResponse = $apOfficerOrderService->showOfficeOrder($request);

        if (isSuccessResponse($showOfficeOrderResponse)) {
            $response = responseFormat('success', $showOfficeOrderResponse['data']);
        } else {
            $response = responseFormat('error', $showOfficeOrderResponse['data']);
        }

        return response()->json($response);

    }

    public function generateOfficeOrder(Request $request, ApOfficerOrderService $apOfficerOrderService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'audit_plan_id' => 'required',
            'annual_plan_id' => 'required',
            'memorandum_no' => 'required',
            'memorandum_date' => 'required',
            'heading_details' => 'required',
            'advices' => 'required',
            'order_cc_list' => 'required',
            'cc_sender_details' => 'required',
            'cdesk' => 'required|json',
        ])->validate();

        $responseData = $apOfficerOrderService->generateOfficeOrder($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function storeOfficeOrderApprovalAuthority(Request $request, ApOfficerOrderService $apOfficerOrderService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'ap_office_order_id' => 'required|integer',
            'annual_plan_id' => 'required|integer',
            'audit_plan_id' => 'required|integer',
            'office_id' => 'required|integer',
            'unit_id' => 'required|integer',
            'unit_name_en' => 'required',
            'unit_name_bn' => 'required',
            'officer_type' => 'required',
            'employee_id' => 'required|integer',
            'employee_name_en' => 'required',
            'employee_name_bn' => 'required',
            'employee_designation_id' => 'required|integer',
            'employee_designation_en' => 'required',
            'employee_designation_bn' => 'required',
            'received_by' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $responseData = $apOfficerOrderService->storeOfficeOrderApprovalAuthority($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

    public function approveOfficeOrder(Request $request, ApOfficerOrderService $apOfficerOrderService, MISAndDashboardService $misAndDashboardService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'ap_office_order_id' => 'required|integer',
            'annual_plan_id' => 'required|integer',
            'audit_plan_id' => 'required|integer',
            'approved_status' => 'required',
            'cdesk' => 'required|json',
        ])->validate();

        $responseData = $apOfficerOrderService->approveOfficeOrder($request);
        $misAndDashboardService->storeAuditPlanTeamInfo($request);

        if (isSuccessResponse($responseData)) {
            $response = responseFormat('success', $responseData['data']);
        } else {
            $response = responseFormat('error', $responseData['data']);
        }

        return response()->json($response);
    }

}
