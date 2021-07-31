<?php

namespace App\Http\Controllers;

use App\Models\ApEntityAuditPlan;
use App\Repository\ApEntityAuditPlanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApEntityAuditPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request, ApEntityAuditPlanRepository $apEntityAuditPlanRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'fiscal_year_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $all_plans = $apEntityAuditPlanRepository->allEntityAuditPlanLists($request);

        if (isSuccessResponse($all_plans)) {
            $response = responseFormat('success', $all_plans['data']);
        } else {
            $response = responseFormat('error', $all_plans['data']);
        }
        return response()->json($response);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ApEntityAuditPlanRepository $apEntityAuditPlanRepository): \Illuminate\Http\JsonResponse
    {
        return response()->json(responseFormat('success', []));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, ApEntityAuditPlanRepository $apEntityAuditPlanRepository)
    {
        Validator::make($request->all(), [
            'entity_id' => 'required|integer',
            'yearly_plan_rp_id' => 'required|integer',
            'cdesk' => 'required|json',
        ])->validate();

        $show_plan = $apEntityAuditPlanRepository->showEntityAuditPlan($request);

        if (isSuccessResponse($show_plan)) {
            $response = responseFormat('success', 'Successfully Saved Plan');
        } else {
            $response = responseFormat('error', $show_plan['data']);
        }
        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, ApEntityAuditPlanRepository $apEntityAuditPlanRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'plan' => 'required|json',
            'cdesk' => 'required|json',
        ])->validate();

        $add_plan = $apEntityAuditPlanRepository->storeDraftAuditPlan($request);

        if (isSuccessResponse($add_plan)) {
            $response = responseFormat('success', 'Successfully Saved Plan');
        } else {
            $response = responseFormat('error', $add_plan['data']);
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ApEntityAuditPlan $apEntityAuditPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApEntityAuditPlan $apEntityAuditPlan)
    {
        //
    }
}
