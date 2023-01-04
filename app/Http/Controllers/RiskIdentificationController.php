<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RiskIdentification;

class RiskIdentificationController extends Controller
{
    public function getParentAreas(Request $request)
    {
        try {

            $list =  RiskIdentification::where('assessment_sector_id', $request->get('assessment_sector_id'))
            ->where('assessment_sector_type', $request->get('assessment_sector_type'))
            ->whereNull('parent_area_id')
            ->pluck('audit_area_id');

            $response = responseFormat('success', $list);

        }
        catch (\Exception $exception) {

            $response = responseFormat('error', $exception->getMessage());

        }

        return response()->json($response);
    }

    public function index(Request $request)
    {
        try {

            $list =  RiskIdentification::where('assessment_sector_id', $request->get('assessment_sector_id'))
            ->where('assessment_sector_type', $request->get('assessment_sector_type'))
            ->where('parent_area_id', $request->get('parent_area_id'))
            ->get();

            $response = responseFormat('success', $list);

        }
        catch (\Exception $exception) {

            $response = responseFormat('error', $exception->getMessage());

        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $auditAssessmentArea = new RiskIdentification();
            $auditAssessmentArea->parent_area_id = $request->parent_area_id;
            $auditAssessmentArea->audit_area_id = $request->audit_area_id;
            $auditAssessmentArea->assessment_sector_id = $request->assessment_sector_id;
            $auditAssessmentArea->assessment_sector_type = $request->assessment_sector_type;
            $auditAssessmentArea->risk_name = $request->risk_name;
            $auditAssessmentArea->is_latest = 1;
            $auditAssessmentArea->creator_id = $request->creator_id;
            $auditAssessmentArea->updater_id = $request->updater_id;
            $auditAssessmentArea->save();

            DB::commit();

            $response = responseFormat('success', 'Save Successfully');
        }
        catch (\Exception $exception) {

            DB::rollBack();

            $response = responseFormat('error', $exception->getMessage());

        }

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $auditAssessmentArea = RiskIdentification::find($id);;
            $auditAssessmentArea->parent_area_id = $request->parent_area_id;
            $auditAssessmentArea->audit_area_id = $request->audit_area_id;
            $auditAssessmentArea->assessment_sector_id = $request->assessment_sector_id;
            $auditAssessmentArea->assessment_sector_type = $request->assessment_sector_type;
            $auditAssessmentArea->risk_name = $request->risk_name;
            $auditAssessmentArea->updater_id = $request->updater_id;
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

            $auditAssessmentArea = RiskIdentification::find($id);
            $auditAssessmentArea->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
