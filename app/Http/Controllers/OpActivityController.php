<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpActivity\SaveRequest;
use App\Http\Requests\OpActivity\SearchActivities;
use App\Http\Requests\OpActivity\ShowOrDeleteRequest;
use App\Http\Requests\OpActivity\UpdateRequest;
use App\Models\OpActivity;
use App\Repository\OpActivityRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OpActivityController extends Controller
{
    public function index(Request $request, OpActivityRepository $opActivity): \Illuminate\Http\JsonResponse
    {
        try {
            $response = responseFormat('success', $opActivity->allActivities($request));
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function findActivities(SearchActivities $request, OpActivityRepository $opActivity): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $opActivity->findActivities($request);
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function showActivitiesByFiscalYear(Request $request, OpActivityRepository $opActivity): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), ['fiscal_year_id' => 'integer|required'])->validate();
        try {
            $data = $opActivity->showActivitiesByFiscalYear($request);
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function showActivityMilestones(Request $request, OpActivityRepository $opActivity): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), ['activity_id' => 'integer|required'])->validate();
        try {
            $data = $opActivity->showActivityMilestones($request);

            $response = responseFormat('success', $data);
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();
            if ($validated['activity_parent_id'] && $validated['activity_parent_id'] > 0) {
                $validated['is_parent'] = 0;
            }
            $validated['duration_id'] = $this->durationIdFromFiscalYear($validated['fiscal_year_id']);
            OpActivity::create($validated);
            $response = responseFormat('success', 'Operational Plan Activity Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    public function show(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $opActivity = OpActivity::with('activity_output.plan_outcome.plan_duration')->where('id', $request->activity_id)->first();

        if (!empty($opActivity)) {
            $response = responseFormat('success', $opActivity);
        } else {
            $response = responseFormat('error', 'Not Found');
        }

        return response()->json($response);
    }

    public function update(UpdateRequest $request): \Illuminate\Http\JsonResponse
    {
        $opActivity = OpActivity::find($request->activity_id);
        try {
            $validated = $request->validated();
            if ($validated['activity_parent_id']) {
                $validated['is_parent'] = $validated['activity_parent_id'] > 0 ? 0 : 1;
            }
            $opActivity->update($validated);
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function destroy(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            OpActivity::find($request->activity_id)->delete();
            $response = responseFormat('success', 'Successfully Deleted');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
