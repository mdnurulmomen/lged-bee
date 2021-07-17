<?php

namespace App\Http\Controllers;

use App\Models\OpYearlyAuditCalendar;
use App\Repository\OpYearlyAuditCalendarRepository;
use Illuminate\Http\Request;

class OpYearlyAuditCalendarController extends Controller
{
    public function index(Request $request, OpYearlyAuditCalendarRepository $opYearlyAuditCalendar): \Illuminate\Http\JsonResponse
    {
        try {
            $response = responseFormat('success', $opYearlyAuditCalendar->allCalendarLists($request));
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\OpYearlyAuditCalendar $opYearlyAuditCalendar
     * @return \Illuminate\Http\Response
     */
    public function show(OpYearlyAuditCalendar $opYearlyAuditCalendar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\OpYearlyAuditCalendar $opYearlyAuditCalendar
     * @return \Illuminate\Http\Response
     */
    public function edit(OpYearlyAuditCalendar $opYearlyAuditCalendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\OpYearlyAuditCalendar $opYearlyAuditCalendar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OpYearlyAuditCalendar $opYearlyAuditCalendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\OpYearlyAuditCalendar $opYearlyAuditCalendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(OpYearlyAuditCalendar $opYearlyAuditCalendar)
    {
        //
    }
}
