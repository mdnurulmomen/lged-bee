<?php

namespace App\Http\Controllers;

use App\Http\Requests\Query\SaveRequest;
use App\Http\Requests\Query\ShowOrDeleteRequest;
use App\Http\Requests\Query\UpdateRequest;
use App\Models\Query;
use Illuminate\Http\Request;

class XAuditQueryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $audit_query = Query::with('cost_center_type')->paginate($request->per_page);
        } else {
            $audit_query = Query::with('cost_center_type')->get();
        }

        if ($audit_query) {
            $response = responseFormat('success', $audit_query);
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
            $request->validated();
            foreach ($request->query_title_bn as $key => $query_title_bn){
                $data['cost_center_type_id'] = $request->cost_center_type_id;
                $data['query_title_bn'] = $query_title_bn;
                $data['query_title_en'] = $request->query_title_bn[$key];
                Query::create($data);
            }

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
        $fiscal_year = Query::findOrFail($request->fiscal_year_id);
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
        $query_id = Query::find($request->id);
        try {
            $query_id->update($request->validated());
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function costCenterTypeWiseQuery(Request $request)
    {
        try {
            $query_list = Query::where('cost_center_type_id',$request->cost_center_type_id)->get();
            return ['status' => 'success', 'data' => $query_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Query $Query
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ShowOrDeleteRequest $request)
    {
        try {
            Query::find($request->audit_query_id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
