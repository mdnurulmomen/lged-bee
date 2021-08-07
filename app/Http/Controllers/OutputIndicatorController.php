<?php

namespace App\Http\Controllers;

use App\Repository\OutcomeIndicatorRepo;
use App\Http\Requests\OutcomeIndicator\CreateOutcomeIndicatorRequest;
use App\Http\Requests\OutcomeIndicator\UpdateOutcomeIndicatorRequest;
use Illuminate\Http\Request;

class OutputIndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(OutcomeIndicatorRepo $indecator): \Illuminate\Http\JsonResponse
    {

        try {
            $response = responseFormat('success', $indecator->index());
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateOutcomeIndicatorRequest $request, OutcomeIndicatorRepo $indecator): \Illuminate\Http\JsonResponse
    {
        try {
            $indecator->store($request);
            $response = responseFormat('success', 'Successfully Saved.');
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function show(Request $request, OutcomeIndicatorRepo $indecator): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $indecator->show($request));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UpdateOutcomeIndicatorRequest $request, OutcomeIndicatorRepo $indecator): \Illuminate\Http\JsonResponse
    {
        try {
            $indecator->update($request);
            $response = responseFormat('success', 'Successfully Saved.');
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ApEntityAuditPlan $apEntityAuditPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, OutcomeIndicatorRepo $indecator)
    {
        try {
            $indecator->destroy($request->id);
            $response = responseFormat('success', 'Successfully deleted.');
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
