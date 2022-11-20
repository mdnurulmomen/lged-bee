<?php

namespace App\Http\Controllers;

use App\Models\RiskMatrix;
use Illuminate\Http\Request;

class RiskMatrixController extends Controller
{
    public function index()
    {
        try {
            $list =  RiskMatrix::with(['riskAssessmentLikelihood', 'riskAssessmentImpact', 'riskLevel'])->get();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {

            $xRiskFactorImpact = new RiskMatrix();
            $xRiskFactorImpact->x_risk_assessment_likelihood_id = $request->x_risk_assessment_likelihood_id;
            $xRiskFactorImpact->x_risk_assessment_impact_id = $request->x_risk_assessment_impact_id;
            $xRiskFactorImpact->x_risk_level_id = $request->x_risk_level_id;
            $xRiskFactorImpact->priority = $request->priority;
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

            $xRiskFactorRating = RiskMatrix::find($id);
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

            $xRiskFactorImpact = RiskMatrix::find($id);
            $xRiskFactorImpact->x_risk_assessment_likelihood_id = $request->x_risk_assessment_likelihood_id;
            $xRiskFactorImpact->x_risk_assessment_impact_id = $request->x_risk_assessment_impact_id;
            $xRiskFactorImpact->x_risk_level_id = $request->x_risk_level_id;
            $xRiskFactorImpact->priority = $request->priority;
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

            $xRiskImpact = RiskMatrix::find($id)->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
