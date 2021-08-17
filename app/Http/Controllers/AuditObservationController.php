<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\AuditObservationRepo;
use App\Http\Requests\AuditObservation\Create;
use App\Http\Requests\AuditObservation\Update;

class AuditObservationController extends Controller
{

    public function removeAttachment(Request $request, AuditObservationRepo $observation): \Illuminate\Http\JsonResponse
    {
        try {
            $observation->removeAttachment($request);
            $response = responseFormat('success', 'Attachment removed.');
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function search(Request $request, AuditObservationRepo $observation): \Illuminate\Http\JsonResponse
    {
        try {
            $response = responseFormat('success', $observation->search($request));
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(AuditObservationRepo $observation): \Illuminate\Http\JsonResponse
    {
        try {
            $response = responseFormat('success', $observation->index());
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
    public function store(Create $request, AuditObservationRepo $observation): \Illuminate\Http\JsonResponse
    {
        try {
            $observation->store($request);
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

    public function show(Request $request, AuditObservationRepo $observation): \Illuminate\Http\JsonResponse
    {
        try {
            $data = responseFormat('success', $observation->show($request));
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
    public function update(Update $request, AuditObservationRepo $observation): \Illuminate\Http\JsonResponse
    {
        try {
            $observation->update($request);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, AuditObservationRepo $observation)
    {
        try {
            $observation->destroy($request);
            $response = responseFormat('success', 'Successfully deleted.');
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
