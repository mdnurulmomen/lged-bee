<?php

namespace App\Http\Controllers;

use App\Models\XAuditAssessmentCriteria;
use Illuminate\Http\Request;

class XAuditAssessmentCriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $criteriaList = XAuditAssessmentCriteria::paginate($request->per_page);
        } else {
            $criteriaList = XAuditAssessmentCriteria::get();
        }

        if ($criteriaList) {
            $response = responseFormat('success', $criteriaList);
        } else {
            $response = responseFormat('error', 'Criteria Not Found');
        }
        return response()->json($response, 200);
    }


    public function loadCategoryWiseCriteriaList(Request $request): \Illuminate\Http\JsonResponse
    {
        $criteriaList = XAuditAssessmentCriteria::where('category_id',$request->category_id)->get();
        if ($criteriaList) {
            $response = responseFormat('success', $criteriaList);
        } else {
            $response = responseFormat('error', 'Criteria Not Found');
        }
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            XAuditAssessmentCriteria::create([
                'category_id' => $request->category_id,
                'category_title_en' => $request->category_title_en,
                'category_title_bn' => $request->category_title_bn,
                'name_en' => $request->name_en,
                'name_bn' => $request->name_bn,
            ]);
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
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $criteriaInfo = XAuditAssessmentCriteria::findOrFail($request->criteria_id);
        if ($criteriaInfo) {
            $response = responseFormat('success', $criteriaInfo);
        } else {
            $response = responseFormat('error', 'Criteria Not Found');
        }
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $criteria = XAuditAssessmentCriteria::find($request->criteria_id);
            $criteria->category_id = $request->category_id;
            $criteria->category_title_en = $request->category_title_en;
            $criteria->category_title_bn = $request->category_title_bn;
            $criteria->name_en = $request->name_en;
            $criteria->name_bn = $request->name_bn;
            $criteria->save();

            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
