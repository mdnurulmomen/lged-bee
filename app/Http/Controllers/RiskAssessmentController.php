<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditAssessmentArea;

class RiskAssessmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $list =  AuditAssessmentArea::with(['auditAssessmentAreaRisks.xRiskAssessmentImpact', 'auditAssessmentAreaRisks.xRiskAssessmentLikelihood'])
            ->where('assessment_sector_id', $request->get('id'))
            ->where('assessment_sector_type', $request->get('type'))
            ->where('assessment_type', $request->get('assessment_type'))
            ->where('is_latest', 1)
            ->get();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        AuditAssessmentArea::where('audit_area_id', $request->audit_area_id)
        ->where('assessment_sector_id', $request->assessment_sector_id)
        ->where('assessment_sector_type', $request->assessment_sector_type)
        ->update([
            'is_latest' => 0
        ]);

        try {
            $auditAssessmentArea = new AuditAssessmentArea();
            $auditAssessmentArea->audit_area_id = $request->audit_area_id;
            $auditAssessmentArea->assessment_sector_id = $request->assessment_sector_id;
            $auditAssessmentArea->assessment_sector_type = $request->assessment_sector_type;
            $auditAssessmentArea->assessment_type = $request->assessment_type;
            $auditAssessmentArea->is_latest = 1;
            $auditAssessmentArea->creator_id = $request->creator_id;
            $auditAssessmentArea->updater_id = $request->updater_id;
            $auditAssessmentArea->save();

            foreach ($request->audit_assessment_area_risks as $auditAssessmentAreaRisk) {

                $auditAssessmentArea->auditAssessmentAreaRisks()->create([
                    'sub_area_id' => $auditAssessmentAreaRisk['sub_area_id'],
                    'sub_area_name' => $auditAssessmentAreaRisk['sub_area_name'],
                    'inherent_risk_id' => $auditAssessmentAreaRisk['inherent_risk_id'],
                    'inherent_risk' => $auditAssessmentAreaRisk['inherent_risk'],
                    'risk_level' => $auditAssessmentAreaRisk['risk_level'],
                    'priority' => $auditAssessmentAreaRisk['priority'],
                    'x_risk_assessment_impact_id' => $auditAssessmentAreaRisk['x_risk_assessment_impact_id'],
                    'x_risk_assessment_likelihood_id' => $auditAssessmentAreaRisk['x_risk_assessment_likelihood_id'],
                    'control_system' => $auditAssessmentAreaRisk['control_system'],
                    'risk_owner_id' => $auditAssessmentAreaRisk['risk_owner_id'],
                    'risk_owner_name' => $auditAssessmentAreaRisk['risk_owner_name'],
                    'process_owner_id' => $auditAssessmentAreaRisk['process_owner_id'],
                    'process_owner_name' => $auditAssessmentAreaRisk['process_owner_name'],
                    'control_owner_id' => $auditAssessmentAreaRisk['control_owner_id'],
                    'control_owner_name' => $auditAssessmentAreaRisk['control_owner_name'],
                ]);

            }

            DB::commit();

            $response = responseFormat('success', 'Save Successfully');
        }
        catch (\Exception $exception) {

            DB::rollBack();

            $response = responseFormat('error', $exception->getMessage());

        }

        return response()->json($response);
    }

    /*
    public function show($id)
    {
        try {

            $xRiskFactorRating = AuditAssessmentArea::find($id);
            $response = responseFormat('success', $xRiskFactorRating);

        }catch () {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
    */

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $auditAssessmentArea = AuditAssessmentArea::find($id);;
            $auditAssessmentArea->audit_area_id = $request->audit_area_id;
            $auditAssessmentArea->assessment_sector_id = $request->assessment_sector_id;
            $auditAssessmentArea->assessment_sector_type = $request->assessment_sector_type;
            $auditAssessmentArea->updater_id = $request->updater_id;
            $auditAssessmentArea->save();

            $auditAssessmentArea->auditAssessmentAreaRisks()->delete();

            foreach ($request->audit_assessment_area_risks as $auditAssessmentAreaRisk) {

                $auditAssessmentArea->auditAssessmentAreaRisks()->create([
                    'inherent_risk' => $auditAssessmentAreaRisk['inherent_risk'],
                    'x_risk_assessment_impact_id' => $auditAssessmentAreaRisk['x_risk_assessment_impact_id'],
                    'x_risk_assessment_likelihood_id' => $auditAssessmentAreaRisk['x_risk_assessment_likelihood_id'],
                    'control_system' => $auditAssessmentAreaRisk['control_system'],
                    'control_effectiveness' => $auditAssessmentAreaRisk['control_effectiveness'],
                    'residual_risk' => $auditAssessmentAreaRisk['residual_risk'],
                    'recommendation' => $auditAssessmentAreaRisk['recommendation'],
                    'implemented_by' => $auditAssessmentAreaRisk['implemented_by'],
                    'implementation_period' => $auditAssessmentAreaRisk['implementation_period'],
                ]);

            }

            DB::commit();

            $response = responseFormat('success', 'Updated Successfully');
        }
        catch (\Exception $exception) {

            DB::rollBack();

            $response = responseFormat('error', $exception->getMessage());

        }

        return response()->json($response);
    }

    public function delete($id)
    {
        try {

            $auditAssessmentArea = AuditAssessmentArea::find($id);

            $updatePreviousAssessment = AuditAssessmentArea::where('audit_area_id', $auditAssessmentArea->audit_area_id)
            ->where('assessment_sector_id', $auditAssessmentArea->assessment_sector_id)
            ->where('assessment_sector_type', $auditAssessmentArea->assessment_sector_type)
            ->latest()
            ->first()
            ->update([
                'is_latest' => 1
            ]);

            $auditAssessmentArea->auditAssessmentAreaRisks()->delete();
            $auditAssessmentArea->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
