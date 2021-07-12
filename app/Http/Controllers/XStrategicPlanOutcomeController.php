<?php

namespace App\Http\Controllers;

use App\Http\Requests\StrategicPlanOutcome\SaveRequest;
use App\Http\Requests\StrategicPlanOutcome\ShowOrDeleteRequest;
use App\Http\Requests\StrategicPlanOutcome\UpdateRequest;
use App\Models\XStrategicPlanOutcome;
use Illuminate\Http\Request;

class XStrategicPlanOutcomeController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $planOutcomes = XStrategicPlanOutcome::with('plan_duration')->paginate($request->per_page);
        } else {
            $planOutcomes = XStrategicPlanOutcome::with('plan_duration')->get();
        }

        if ($planOutcomes) {
            $response = responseFormat('success', $planOutcomes);
        } else {
            $response = responseFormat('error', 'Fiscal Year Not Found');
        }
        return response()->json($response, 200);
    }

    public function remarksByOutcomeId(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $remarks = XStrategicPlanOutcome::where('id', $request->outcome_id)->first(['id', 'remarks']);
            $response = responseFormat('success', $remarks);
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }
        return response()->json($response);
    }

    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            XStrategicPlanOutcome::create($request->validated());
            $response = responseFormat('success', 'Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }
        return response()->json($response);
    }

    public function show(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $plan_outcome = XStrategicPlanOutcome::findOrFail($request->outcome_id);
        return response()->json($plan_outcome);
    }

    public function update(UpdateRequest $request)
    {
        $plan_outcome = XStrategicPlanOutcome::find($request->outcome_id);
        try {
            $plan_outcome->update($request->validated());
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function destroy(ShowOrDeleteRequest $request)
    {
        try {
            XStrategicPlanOutcome::find($request->outcome_id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
