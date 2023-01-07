<?php

namespace App\Http\Controllers;

use App\Models\XRiskLevel;
use Illuminate\Http\Request;

class XRiskLevelController extends Controller
{
    public function index(Request $request)
    {
        try {
            $type = $request->type;

            $query =  XRiskLevel::query();

            $query->when($type, function ($q, $type) {
                return $q->where('type', $type);
            });

            $list = $query->get();

            $response = responseFormat('success', $list);

        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        try {

            $xRiskFactor = new XRiskLevel();
            $xRiskFactor->level_from = $request->level_from;
            $xRiskFactor->level_to = $request->level_to;
            $xRiskFactor->type = $request->type;
            $xRiskFactor->title_bn = $request->title_bn;
            $xRiskFactor->title_en = $request->title_en;
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

            $xRiskFactor = XRiskLevel::find($id);
            $xRiskFactor->level_from = $request->level_from;
            $xRiskFactor->level_to = $request->level_to;
            $xRiskFactor->type = $request->type;
            $xRiskFactor->title_bn = $request->title_bn;
            $xRiskFactor->title_en = $request->title_en;
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

            $xRiskFactor = XRiskLevel::find($id)->delete();

            $response = responseFormat('success', 'Deleted Successfully');


        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }
}
