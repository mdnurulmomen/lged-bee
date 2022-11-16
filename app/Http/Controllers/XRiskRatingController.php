<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\XRiskFactorRating;

class XRiskRatingController extends Controller
{
    public function index()
    {
        try {
            $list =  XRiskFactorRating::with('xRiskFactor')->get();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {

            $xRiskFactorRating = new XRiskFactorRating();
            $xRiskFactorRating->title_bn = strtolower($request->title_bn);
            $xRiskFactorRating->title_en = strtolower($request->title_en);
            $xRiskFactorRating->rating_value = $request->rating_value;
            $xRiskFactorRating->x_risk_factor_id = $request->x_risk_factor_id;
            $xRiskFactorRating->save();

            $response = responseFormat('success', 'Save Successfully');

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    /*
    public function show($id)
    {
        try {

            $xRiskFactorRating = XRiskFactorRating::find($id);
            $response = responseFormat('success', $xRiskFactorRating);

        }catch () {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
    */

    public function update(Request $request, $id)
    {
        try {

            $xRiskFactorRating = XRiskFactorRating::find($id);
            $xRiskFactorRating->title_bn = strtolower($request->title_bn);
            $xRiskFactorRating->title_en = strtolower($request->title_en);
            $xRiskFactorRating->rating_value = $request->rating_value;
            $xRiskFactorRating->x_risk_factor_id = $request->x_risk_factor_id;
            $xRiskFactorRating->save();

            $response = responseFormat('success', 'Updated Successfully');

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function delete($id)
    {
        try {

            $xRiskFactor = XRiskFactorRating::find($id)->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
