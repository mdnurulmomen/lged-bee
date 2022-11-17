<?php

namespace App\Http\Controllers;

use App\Models\XAuditArea;
use Illuminate\Http\Request;

class XAuditAreaController extends Controller
{
    public function index()
    {
        try {
            $list =  XAuditArea::all();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {

            $xRiskFactorImpact = new XAuditArea();
            $xRiskFactorImpact->name_bn = strtolower($request->name_bn);
            $xRiskFactorImpact->name_en = strtolower($request->name_en);
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

            $xRiskFactorRating = XAuditArea::find($id);
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

            $xRiskFactorImpact = XAuditArea::find($id);
            $xRiskFactorImpact->name_bn = strtolower($request->name_bn);
            $xRiskFactorImpact->name_en = strtolower($request->name_en);
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

            $xRiskImpact = XAuditArea::find($id)->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
