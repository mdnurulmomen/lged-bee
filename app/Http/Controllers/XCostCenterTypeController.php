<?php

namespace App\Http\Controllers;

use App\Http\Requests\XFiscalYear\SaveRequest;
use App\Http\Requests\XFiscalYear\ShowOrDeleteRequest;
use App\Http\Requests\XFiscalYear\UpdateRequest;
use App\Models\CostCenterType;
use Illuminate\Http\Request;

class XCostCenterTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $csot_center_types = CostCenterType::paginate($request->per_page);
        } else {
            $csot_center_types = CostCenterType::all();
        }

        if ($csot_center_types) {
            $response = responseFormat('success', $csot_center_types);
        } else {
            $response = responseFormat('error', 'Cost Center Type Not Found');
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
}
