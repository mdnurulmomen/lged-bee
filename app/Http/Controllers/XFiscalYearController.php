<?php

namespace App\Http\Controllers;

use App\Http\Requests\XFiscalYear\SaveRequest;
use App\Http\Requests\XFiscalYear\ShowOrDeleteRequest;
use App\Http\Requests\XFiscalYear\UpdateRequest;
use App\Models\XFiscalYear;
use Illuminate\Http\Request;

class XFiscalYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $fiscal_years = XFiscalYear::orderBy('start')->paginate($request->per_page);
        } else {
            $fiscal_years = XFiscalYear::orderBy('start')->all();
        }

        if ($fiscal_years) {
            $response = responseFormat('success', $fiscal_years);
        } else {
            $response = responseFormat('error', 'Fiscal Year Not Found');
        }
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            XFiscalYear::create($request->validated());
            $response = responseFormat('success', 'Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $fiscal_year = XFiscalYear::findOrFail($request->fiscal_year_id);
        if ($fiscal_year) {
            $response = responseFormat('success', $fiscal_year);
        } else {
            $response = responseFormat('error', 'Fiscal Year Not Found');
        }
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        $fiscal_year = XFiscalYear::find($request->fiscal_year_id);
        try {
            $fiscal_year->update($request->validated());
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\XFiscalYear $xFiscalYear
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ShowOrDeleteRequest $request)
    {
        try {
            XFiscalYear::find($request->fiscal_year_id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }


    public function currentFiscalYear(ShowOrDeleteRequest $request)
    {
        try {
            $current_fiscal_year = XFiscalYear::where('start', date("Y"))->first();
            $response = responseFormat('success', $current_fiscal_year);
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
