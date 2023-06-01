<?php

namespace App\Http\Controllers;

use App\Http\Requests\XStrategicPlanDuration\SaveRequest;
use App\Http\Requests\XStrategicPlanDuration\ShowOrDeleteRequest;
use App\Http\Requests\XStrategicPlanDuration\UpdateRequest;
use App\Models\XStrategicPlanDuration;
use Illuminate\Http\Request;

class XStrategicPlanDurationController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $planDurations = XStrategicPlanDuration::paginate($request->per_page);
        } else {
            $planDurations = XStrategicPlanDuration::withCount('strategic_plan')->get();
        }

        if ($planDurations) {
            $response = responseFormat('success', $planDurations);
        } else {
            $response = responseFormat('error', 'Fiscal Year Not Found');
        }
        return response()->json($response, 200);
    }

    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            XStrategicPlanDuration::create($request->validated());
            $response = responseFormat('success', 'Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    public function show(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $plan_duration = XStrategicPlanDuration::findOrFail($request->duration_id);
        return response()->json($plan_duration);
    }

    public function update(UpdateRequest $request)
    {
        $plan_duration = XStrategicPlanDuration::find($request->duration_id);
        try {
            $plan_duration->update($request->validated());
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function destroy(ShowOrDeleteRequest $request)
    {
        try {
            $duration = XStrategicPlanDuration::find($request->duration_id);
            if (!empty($duration->strategic_plan)) {
                $response = responseFormat('error', 'Duration period is used');
            } else {
                $duration->delete();
                $response = responseFormat('success', 'Delete Successfull');
            }
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
