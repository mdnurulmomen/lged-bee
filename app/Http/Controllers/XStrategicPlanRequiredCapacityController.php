<?php

namespace App\Http\Controllers;

use App\Http\Requests\XStrategicPlanRequiredCapacity\SaveRequest;
use App\Http\Requests\XStrategicPlanRequiredCapacity\ShowOrDeleteRequest;
use App\Http\Requests\XStrategicPlanRequiredCapacity\UpdateRequest;
use App\Models\XStrategicPlanRequiredCapacity;
use Illuminate\Http\Request;

class XStrategicPlanRequiredCapacityController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $capacities = XStrategicPlanRequiredCapacity::paginate($request->per_page);
        } else {
            $capacities = XStrategicPlanRequiredCapacity::all();
        }

        if ($capacities) {
            $response = responseFormat('success', $capacities);
        } else {
            $response = responseFormat('error', 'Strategic Plan Required Capacity Not Found');
        }
        return response()->json($response, 200);
    }

    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            XStrategicPlanRequiredCapacity::create($request->validated());
            $response = responseFormat('success', 'Strategic Plan Required Capacity Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    public function show(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $capacity = XStrategicPlanRequiredCapacity::findOrFail($request->required_capacity_id);
        return response()->json($capacity);
    }

    public function update(UpdateRequest $request)
    {
        $capacity = XStrategicPlanRequiredCapacity::find($request->required_capacity_id);
        try {
            $capacity->update($request->validated());
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function destroy(ShowOrDeleteRequest $request)
    {
        try {
            XStrategicPlanRequiredCapacity::find($request->required_capacity_id)->delete();
            $response = responseFormat('success', 'Successfully Deleted');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
