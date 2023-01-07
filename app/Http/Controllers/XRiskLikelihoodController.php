<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\XRiskAssessmentLikelihood;

class XRiskLikelihoodController extends Controller
{
    public function index()
    {
        try {
            $list =  XRiskAssessmentLikelihood::all();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {

            $xRiskFactorImpact = new XRiskAssessmentLikelihood();
            $xRiskFactorImpact->title_bn = $request->title_bn;
            $xRiskFactorImpact->title_en = $request->title_en;
            $xRiskFactorImpact->description_bn = $request->description_bn;
            $xRiskFactorImpact->description_en = $request->description_en;
            $xRiskFactorImpact->comment_en = $request->comment_en;
            $xRiskFactorImpact->commnet_bn = $request->commnet_bn;
            $xRiskFactorImpact->likelihood_value = $request->likelihood_value;
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

            $xRiskFactorRating = XRiskAssessmentLikelihood::find($id);
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

            $xRiskFactorImpact = XRiskAssessmentLikelihood::find($id);
            $xRiskFactorImpact->title_bn = $request->title_bn;
            $xRiskFactorImpact->title_en = $request->title_en;
            $xRiskFactorImpact->description_bn = $request->description_bn;
            $xRiskFactorImpact->description_en = $request->description_en;
            $xRiskFactorImpact->comment_en = $request->comment_en;
            $xRiskFactorImpact->commnet_bn = $request->commnet_bn;
            $xRiskFactorImpact->likelihood_value = $request->likelihood_value;
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

            $xRiskImpact = XRiskAssessmentLikelihood::find($id)->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
