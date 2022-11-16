<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\XRiskFactorCriteria;

class XRiskCriterionController extends Controller
{
    public function index()
    {
        try {
            $list =  XRiskFactorCriteria::with('xRiskFactor')->get();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {

            $xRiskFactor = new XRiskFactorCriteria();
            $xRiskFactor->title_bn = strtolower($request->title_bn);
            $xRiskFactor->title_en = strtolower($request->title_en);
            $xRiskFactor->x_risk_factor_id = $request->x_risk_factor_id;
            $xRiskFactor->created_by = $request->created_by;
            $xRiskFactor->updated_by = $request->updated_by;
            $xRiskFactor->save();

            $response = responseFormat('success', 'Save Successfully');

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        try {

            $xRiskFactor = XRiskFactorCriteria::find($id);
            $xRiskFactor->title_bn = strtolower($request->title_bn);
            $xRiskFactor->title_en = strtolower($request->title_en);
            $xRiskFactor->x_risk_factor_id = $request->x_risk_factor_id;
            $xRiskFactor->updated_by = $request->updated_by;
            $xRiskFactor->save();

            $response = responseFormat('success', 'Updated Successfully');

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function delete($id)
    {
        try {

            $xRiskFactor = XRiskFactorCriteria::find($id)->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
