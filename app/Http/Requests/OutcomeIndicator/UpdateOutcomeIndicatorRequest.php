<?php

namespace App\Http\Requests\OutcomeIndicator;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOutcomeIndicatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'duration_id' => 'required|numeric',
            'outcome_id' => 'required|numeric',
            'name_en' => 'required',
            'name_bn' => 'required',
            'frequency_en' => 'required',
            'frequency_bn' => 'required',
            'datasource_en' => 'required',
            'datasource_bn' => 'required',
            'unit_type' => 'required',
            'base_fiscal_year_id' => 'required|numeric',
            'base_value' => 'required',
            'fiscal_year_id.*' => 'required'
        ];
    }
}
