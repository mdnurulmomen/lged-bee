<?php

namespace App\Http\Controllers;

use App\Services\ApOfficerOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApOfficeOrderController extends Controller
{
    public function auditPlanList(Request $request,ApOfficerOrderService $apOfficerOrderService): \Illuminate\Http\JsonResponse
    {
        $auditPlanListResponse = $apOfficerOrderService->auditPlanList($request);

        if (isSuccessResponse($auditPlanListResponse)) {
            $response = responseFormat('success', $auditPlanListResponse['data']);
        } else {
            $response = responseFormat('error', $auditPlanListResponse['data']);
        }

        return response()->json($response);

    }

    public function showOfficeOrder(Request $request,ApOfficerOrderService $apOfficerOrderService): \Illuminate\Http\JsonResponse
    {
        $showOfficeOrderResponse = $apOfficerOrderService->showOfficeOrder($request);

        if (isSuccessResponse($showOfficeOrderResponse)) {
            $response = responseFormat('success', $showOfficeOrderResponse['data']);
        } else {
            $response = responseFormat('error', $showOfficeOrderResponse['data']);
        }

        return response()->json($response);

    }

    public function generateOfficeOrder(Request $request,ApOfficerOrderService $apOfficerOrderService): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'audit_plan_id' => 'required',
            'annual_plan_id' => 'required',
            'memorandum_no' => 'required',
            'memorandum_date' => 'required',
            'heading_details' => 'required',
            'advices' => 'required',
            'order_cc_list' => 'required',
            'cdesk' => 'required|json',
        ])->validate();

        $storeAnnualPlan = $apOfficerOrderService->generateOfficeOrder($request);

        if (isSuccessResponse($storeAnnualPlan)) {
            $response = responseFormat('success', $storeAnnualPlan['data']);
        } else {
            $response = responseFormat('error', $storeAnnualPlan['data']);
        }

        return response()->json($response);
    }

}
