<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\XRiskAssessmentImpact;

class XRiskImpactController extends Controller
{
    public function index()
    {
        try {
            $list =  XRiskAssessmentImpact::orderBy('impact_value','DESC')->get();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {

            $xRiskFactorImpact = new XRiskAssessmentImpact();
            $xRiskFactorImpact->title_bn = $request->title_bn;
            $xRiskFactorImpact->title_en = $request->title_en;
            $xRiskFactorImpact->impact_value = $request->impact_value;
            $xRiskFactorImpact->created_by = $request->created_by;
            $xRiskFactorImpact->updated_by = $request->updated_by;
            $xRiskFactorImpact->save();

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

            $xRiskFactorRating = XRiskAssessmentImpact::find($id);
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

            $xRiskFactorImpact = XRiskAssessmentImpact::find($id);
            $xRiskFactorImpact->title_bn = $request->title_bn;
            $xRiskFactorImpact->title_en = $request->title_en;
            $xRiskFactorImpact->impact_value = $request->impact_value;
            $xRiskFactorImpact->updated_by = $request->updated_by;
            $xRiskFactorImpact->save();

            $response = responseFormat('success', 'Updated Successfully');

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function delete($id)
    {
        try {

            $xRiskImpact = XRiskAssessmentImpact::find($id)->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
