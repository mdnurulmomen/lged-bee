<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpActivityMilestone\ActivityMilestoneRequest;
use App\Http\Requests\OpActivityMilestone\SaveRequest;
use App\Http\Requests\OpActivityMilestone\ShowOrDeleteRequest;
use App\Http\Requests\OpActivityMilestone\UpdateRequest;
use App\Models\OpActivityMilestone;
use App\Models\XFiscalYear;
use App\Models\XStrategicPlanDuration;

class OpActivityMilestoneController extends Controller
{
    public function index(ActivityMilestoneRequest $request): \Illuminate\Http\JsonResponse
    {
        $duration_id = $request->duration_id;

        $milestones = XStrategicPlanDuration::where('id', $duration_id)->with('plan_outcome.plan_output.activities.children.milestones')->get();

        if ($milestones) {
            $response = responseFormat('success', $milestones);
        } else {
            $response = responseFormat('error', 'Activity Milestone Not Found');
        }
        return response()->json($response);
    }

    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated['duration_id'] = XFiscalYear::where('id', $validated['fiscal_year_id'])->first()->duration_id;

            (new \App\Models\OpActivityMilestone)->saveMilestone($validated);

            $response = responseFormat('success', 'Milestone Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }
        return response()->json($response);
    }

    public function show(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $opActivityMilestone = OpActivityMilestone::with('activity')->findOrFail($request->milestone_id);
        return response()->json($opActivityMilestone);
    }

    public function update(UpdateRequest $request)
    {
        $opActivityMilestone = OpActivityMilestone::find($request->milestone_id);
        try {
            $validated = $request->validated();
            $opActivityMilestone->update($validated);
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function destroy(ShowOrDeleteRequest $request)
    {
        try {
            OpActivityMilestone::find($request->milestone_id)->delete();
            $response = responseFormat('success', 'Successfully Deleted');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
