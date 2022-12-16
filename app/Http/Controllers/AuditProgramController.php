<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditProgram;
use App\Exports\AuditProgramsExport;
use Maatwebsite\Excel\Facades\Excel;

class AuditProgramController extends Controller
{
    public function index(Request $request)
    {
        try {
            $list =  AuditProgram::with('procedures')
            ->where('audit_area_id', $request->audit_area_id)
            ->get();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {

            $auditProgram = new AuditProgram();
            $auditProgram->area_index = $request->area_index;
            $auditProgram->category = strtolower($request->category);
            $auditProgram->control_objective = strtolower($request->control_objective);
            $auditProgram->audit_area_id = $request->audit_area_id;
            $auditProgram->save();

            foreach ($request->procedures as $audit_area_procedure) {

                $auditProgram->procedures()->create([
                    'test_procedure' => $audit_area_procedure['test_procedure'],
                    // 'note' => $audit_area_procedure['note'],
                    // 'done_by' => $audit_area_procedure['done_by'],
                    // 'reference' => $audit_area_procedure['reference'],
                ]);

            }

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

            $xRiskFactorRating = AuditProgram::find($id);
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

            $auditProgram = AuditProgram::find($id);
            $auditProgram->area_index = $request->area_index;
            $auditProgram->category = strtolower($request->category);
            $auditProgram->control_objective = strtolower($request->control_objective);
            $auditProgram->audit_area_id = $request->audit_area_id;
            $auditProgram->save();

            $auditProgram->procedures()->delete();

            foreach ($request->procedures as $audit_area_procedure) {

                $auditProgram->procedures()->create([
                    'test_procedure' => $audit_area_procedure['test_procedure'],
                    // 'note' => $audit_area_procedure['note'],
                    // 'done_by' => $audit_area_procedure['done_by'],
                    // 'reference' => $audit_area_procedure['reference'],
                ]);

            }

            $response = responseFormat('success', 'Updated Successfully');

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function delete($id)
    {
        try {

            $auditProgram = AuditProgram::find($id);
            $auditProgram->procedures()->delete();
            $auditProgram->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function export(Request $request)
    {
        try {
            $list =  AuditProgram::with('procedures')
            ->where('audit_area_id', $request->audit_area_id)
            ->get();

            Excel::store(new AuditProgramsExport($list, $request->sectorName, $request->auditAreaName), 'audit-program/programs.xlsx', 'public');

            $response = responseFormat('success', '/storage/audit-program/programs.xlsx');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);

    }
}
