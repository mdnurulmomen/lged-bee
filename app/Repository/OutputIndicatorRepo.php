<?php

namespace App\Repository;

use App\Repository\Contracts\IndicatorInterface;
use App\Models\OutputIndicator;
use App\Models\OutputIndicatorDetail;
use Illuminate\Http\Request;

class OutputIndicatorRepo implements IndicatorInterface
{

    public function __construct(OutputIndicator $indecator)
    {
        $this->indecator = $indecator;
    }

    public function index()
    {
        return $this->indecator->latest()->get();
    }

    public function show(Request $request)
    {
        return $this->indecator->with('details')->findOrFail($request->id);
    }

    public function store(Request $request)
    {
        $indecator = new OutputIndicator();
        $indecator->duration_id = $request->duration_id;
        $indecator->output_id = $request->output_id;
        $indecator->name_en = $request->name_en;
        $indecator->name_bn = $request->name_bn;
        $indecator->frequency_en = $request->frequency_en;
        $indecator->frequency_bn = $request->frequency_bn;
        $indecator->datasource_en = $request->datasource_en;
        $indecator->datasource_bn = $request->datasource_bn;
        $indecator->base_fiscal_year_id = $request->base_fiscal_year_id;
        $indecator->base_value = $request->base_value;
        $indecator->status = $request->status;

        $details = [];
        if ($indecator->save()) {
            foreach ($request->fiscal_year_id as $key => $fiscal_year) {
                $details[] = new OutputIndicatorDetail([
                    'duration_id' => $request->duration_id,
                    'fiscal_year_id' => $fiscal_year,
                    'output_id' => $request->output_id,
                    'unit_type' => $request->unit_type[$key],
                    'target_value' => $request->target_value[$key],
                ]);
            }
        }

        $indecator->details()->saveMany($details);

        return $indecator;
    }

    public function update(Request $request)
    {
        $indecator = $this->indecator->find($request->id);

        $indecator->duration_id = $request->duration_id;
        $indecator->outcome_id = $request->outcome_id;
        $indecator->name_en = $request->name_en;
        $indecator->name_bn = $request->name_bn;
        $indecator->frequency_en = $request->frequency_en;
        $indecator->frequency_bn = $request->frequency_bn;
        $indecator->datasource_en = $request->datasource_en;
        $indecator->datasource_bn = $request->datasource_bn;
        $indecator->base_fiscal_year_id = $request->base_fiscal_year_id;
        $indecator->base_value = $request->base_value;
        $indecator->status = $request->status;

        $details = [];
        if ($indecator->save()) {
            OutputIndicatorDetail::where('outcome_indicator_id', $request->id)->delete();
            foreach ($request->fiscal_year_id as $key => $fiscal_year) {
                $details[] = new OutputIndicatorDetail([
                    'duration_id' => $request->duration_id,
                    'fiscal_year_id' => $fiscal_year,
                    'output_id' => $request->output_id,
                    'unit_type' => $request->unit_type[$key],
                    'target_value' => $request->target_value[$key],
                ]);
            }
        }

        $indecator->details()->saveMany($details);

        return $indecator;
    }

    public function destroy(Request $request)
    {
        return $this->indecator->findOrFail($request->id)->delete();
    }
}
