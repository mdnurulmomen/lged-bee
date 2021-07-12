<?php

namespace App\Http\Controllers;

use App\Http\Requests\XStrategicPlanOutput\SaveRequest;
use App\Http\Requests\XStrategicPlanOutput\ShowOrDeleteRequest;
use App\Http\Requests\XStrategicPlanOutput\UpdateRequest;
use App\Models\XStrategicPlanOutput;
use Illuminate\Http\Request;

class XStrategicPlanOutputController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $planOutputs = XStrategicPlanOutput::paginate($request->per_page);
        } else {
            $planOutputs = XStrategicPlanOutput::with('plan_outcome.plan_duration')->get();
        }

        if ($planOutputs) {
            $response = responseFormat('success', $planOutputs);
        } else {
            $response = responseFormat('error', 'Strategic Plan Output Year Not Found');
        }
        return response()->json($response, 200);
    }

    public function outputByOutcome(Request $request)
    {
        try {
            $outcomes = XStrategicPlanOutput::where('outcome_id', $request->outcome_id)->with('plan_outcome')->get();
            $response = responseFormat('success', $outcomes);
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            XStrategicPlanOutput::create($request->validated());
            $response = responseFormat('success', 'Strategic Plan Output Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    public function show(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $planOutput = XStrategicPlanOutput::findOrFail($request->output_id);
        return response()->json($planOutput);
    }

    public function update(UpdateRequest $request)
    {
        $planOutput = XStrategicPlanOutput::find($request->output_id);
        try {
            $planOutput->update($request->validated());
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function destroy(ShowOrDeleteRequest $request)
    {
        try {
            XStrategicPlanOutput::find($request->output_id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
