<?php

namespace App\Services;
use App\Models\XRiskFactor;
use Illuminate\Http\Request;
use DB;

class XRiskFactorService
{
    public function list(): array
    {
        try {
            $list =  XRiskFactor::all();

            return ['status' => 'success', 'data' => $list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function store(Request $request): array
    {
        try {

            $xRiskFactor = new XRiskFactor();
            $xRiskFactor->title_bn = $request->title_bn;
            $xRiskFactor->title_en = $request->title_en;
            $xRiskFactor->risk_weight = $request->risk_weight;
            $xRiskFactor->save();

            return ['status' => 'success', 'data' => 'Save Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function update(Request $request, $id): array
    {
        try {

            $xRiskFactor = XRiskFactor::find($id);
            $xRiskFactor->title_bn = $request->title_bn;
            $xRiskFactor->title_en = $request->title_en;
            $xRiskFactor->risk_weight = $request->risk_weight;
            $xRiskFactor->save();

            return ['status' => 'success', 'data' => 'Updated Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function delete($id): array
    {
        try {

            $xRiskFactor = XRiskFactor::find($id)->delete();

            return ['status' => 'success', 'data' => 'Deleted Successfully'];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

}
