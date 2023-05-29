<?php

namespace App\Http\Controllers;

use App\Models\AuditAssessmentAreaRisk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditAssessmentArea;
use Illuminate\Support\Arr;

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

    public function auditSubAreaRiskInfo(Request $request)
    {
        try {

            $sub_area_risk_info = AuditAssessmentAreaRisk::with('xRiskAssessmentImpact','xRiskAssessmentLikelihood')->find($request->id);
            $response = responseFormat('success', $sub_area_risk_info);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

//        AuditAssessmentArea::where('audit_area_id', $request->audit_area_id)
//        ->where('assessment_sector_id', $request->assessment_sector_id)
//        ->where('assessment_sector_type', $request->assessment_sector_type)
//        ->update([
//            'is_latest' => 0
//        ]);

        try {
            $auditAssessmentArea = AuditAssessmentArea::where('audit_area_id', $request->audit_area_id)
                ->where('assessment_sector_id', $request->assessment_sector_id)
                ->where('assessment_sector_type', $request->assessment_sector_type)
                ->where('assessment_type', $request->assessment_type)
                ->first();

            if(!$auditAssessmentArea){
                $auditAssessmentArea = new AuditAssessmentArea();
                $auditAssessmentArea->audit_area_id = $request->audit_area_id;
                $auditAssessmentArea->assessment_sector_id = $request->assessment_sector_id;
                $auditAssessmentArea->assessment_sector_type = $request->assessment_sector_type;
                $auditAssessmentArea->assessment_type = $request->assessment_type;
                $auditAssessmentArea->is_latest = 1;
                $auditAssessmentArea->creator_id = $request->creator_id;
                $auditAssessmentArea->updater_id = $request->updater_id;
                $auditAssessmentArea->save();
            }

            foreach ($request->audit_assessment_area_risks as $auditAssessmentAreaRisk) {

                $auditAssessmentArea->auditAssessmentAreaRisks()->updateOrCreate([
                        'inherent_risk_id' => $auditAssessmentAreaRisk['inherent_risk_id'],'assessment_type' => $request->assessment_type],
                    [
                    'assessment_type' => $request->assessment_type,
                    'sub_area_id' => $auditAssessmentAreaRisk['sub_area_id'],
                    'sub_area_name' => $auditAssessmentAreaRisk['sub_area_name'],
                    'inherent_risk_id' => $auditAssessmentAreaRisk['inherent_risk_id'],
                    'inherent_risk' => $auditAssessmentAreaRisk['inherent_risk'],
                    'risk_level' => $auditAssessmentAreaRisk['risk_level'],
                    'priority' => $auditAssessmentAreaRisk['priority'],
                    'x_risk_assessment_impact_id' => $auditAssessmentAreaRisk['x_risk_assessment_impact_id'],
                    'x_risk_assessment_likelihood_id' => $auditAssessmentAreaRisk['x_risk_assessment_likelihood_id'],
                    'control_system' => $auditAssessmentAreaRisk['control_system'],
                    'issue_no' => $auditAssessmentAreaRisk['issue_no'],
                    'risk_owner_id' => Arr::exists($auditAssessmentAreaRisk, 'risk_owner_id') ? $auditAssessmentAreaRisk['risk_owner_id'] : 0,
                    'risk_owner_name' => $auditAssessmentAreaRisk['risk_owner_name'],
                    'process_owner_id' => Arr::exists($auditAssessmentAreaRisk, 'process_owner_id') ? $auditAssessmentAreaRisk['process_owner_id'] : 0,
                    'process_owner_name' => $auditAssessmentAreaRisk['process_owner_name'],
                    'control_owner_id' => Arr::exists($auditAssessmentAreaRisk, 'control_owner_id') ? $auditAssessmentAreaRisk['control_owner_id'] : 0,
                    'control_owner_name' => $auditAssessmentAreaRisk['control_owner_name'],
                    'recommendation' => Arr::exists($auditAssessmentAreaRisk, 'comments') ? $auditAssessmentAreaRisk['comments'] : '',

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

            $auditAssessmentArea = AuditAssessmentAreaRisk::find($id);

            $auditAssessmentArea->sub_area_id = $request->sub_area_id ? $request->sub_area_id : $auditAssessmentArea->sub_area_id;
            $auditAssessmentArea->sub_area_name = $request->sub_area_name ? $request->sub_area_name : $auditAssessmentArea->sub_area_name;
            $auditAssessmentArea->inherent_risk_id = $request->inherent_risk_id ? $request->inherent_risk_id : $auditAssessmentArea->inherent_risk_id;
            $auditAssessmentArea->inherent_risk = $request->inherent_risk ? $request->inherent_risk : $auditAssessmentArea->inherent_risk;
            $auditAssessmentArea->x_risk_assessment_impact_id = $request->x_risk_assessment_impact_id ? $request->x_risk_assessment_impact_id : $auditAssessmentArea->x_risk_assessment_impact_id;
            $auditAssessmentArea->x_risk_assessment_likelihood_id = $request->x_risk_assessment_likelihood_id ? $request->x_risk_assessment_likelihood_id : $auditAssessmentArea->x_risk_assessment_likelihood_id;
            $auditAssessmentArea->risk_level = $request->risk_level ? $request->risk_level : $auditAssessmentArea->risk_level;
            $auditAssessmentArea->priority = $request->priority ? $request->priority : $auditAssessmentArea->priority;
            $auditAssessmentArea->issue_no = $request->issue_no ? $request->issue_no : $auditAssessmentArea->issue_no;
            $auditAssessmentArea->risk_owner_id = $request->risk_owner_id ? $request->risk_owner_id : $auditAssessmentArea->risk_owner_id;
            $auditAssessmentArea->risk_owner_name = $request->risk_owner_name ? $request->risk_owner_name : $auditAssessmentArea->risk_owner_name;
            $auditAssessmentArea->process_owner_id = $request->process_owner_id ? $request->process_owner_id : $auditAssessmentArea->process_owner_id;
            $auditAssessmentArea->process_owner_name = $request->process_owner_name ? $request->process_owner_name : $auditAssessmentArea->process_owner_name;
            $auditAssessmentArea->control_owner_id = $request->control_owner_id ? $request->control_owner_id : $auditAssessmentArea->control_owner_id;
            $auditAssessmentArea->control_owner_name = $request->control_owner_name ? $request->control_owner_name : $auditAssessmentArea->control_owner_name;
            $auditAssessmentArea->control_system = $request->control_system ? $request->control_system : $auditAssessmentArea->control_system;
            $auditAssessmentArea->save();

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
