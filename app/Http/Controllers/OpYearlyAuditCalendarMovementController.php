<?php

namespace App\Http\Controllers;

use App\Models\OpYearlyAuditCalendarMovement;
use App\Repository\OpYearlyAuditCalendarMovementRepository;
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
