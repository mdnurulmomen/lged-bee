<?php

namespace App\Http\Controllers;

use App\Models\OpYearlyAuditCalendarMovement;
use App\Repository\OpYearlyAuditCalendarMovementRepository;
use App\Repository\OpYearlyAuditCalendarRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OpYearlyAuditCalendarMovementController extends Controller
{

    public function index(Request $request)
    {
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, OpYearlyAuditCalendarMovementRepository $opYearlyAuditCalendarMovementRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'designations' => 'required|json',
            'audit_calendar_master_id' => 'required|integer',
        ])->validate();

        $createMovement = $opYearlyAuditCalendarMovementRepository->forwardAuditCalendar($request);
        if ($createMovement['status'] === 'success') {
            $response = responseFormat('success', 'Successfully Created');
        } else {
            $response = responseFormat('error', $createMovement['data']);
        }
        return response()->json($response);
    }

    public function movementHistory(Request $request, OpYearlyAuditCalendarMovementRepository $opYearlyAuditCalendarMovementRepository): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'op_yearly_calendar_id' => 'required|integer',
        ])->validate();

        try {
            $response = responseFormat('success', $opYearlyAuditCalendarMovementRepository
                ->movementHistory($request));
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function changeStatus(Request $request, OpYearlyAuditCalendarRepository $opYearlycalendar): \Illuminate\Http\JsonResponse
    {
        Validator::make($request->all(), [
            'id' => 'required|integer',
            'status' => 'required|string',
        ])->validate();

        try {
            $opYearlycalendar->changeStatus($request);
            return response()->json(responseFormat('success', 'Status Changed'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function update(Request $request)
    {
    }

    public function destroy(OpYearlyAuditCalendarMovement $opYearlyAuditCalendarApprovalMovement)
    {
        //
    }
}
