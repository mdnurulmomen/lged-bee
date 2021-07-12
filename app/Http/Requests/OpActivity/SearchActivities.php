<?php

namespace App\Http\Requests\OpActivity;

use Illuminate\Foundation\Http\FormRequest;

class SearchActivities extends FormRequest
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
            'output_id' => 'integer|required_without_all:fiscal_year_id,outcome_id',
            'outcome_id' => 'integer|required_without_all:fiscal_year_id,output_id',
            'fiscal_year_id' => 'integer|required_without_all:outcome_id,output_id',
        ];
    }

    public function messages(): array
    {
        return [
            'output_id.required' => 'Output Required',
            'output_id.integer' => 'Please specify output.',
            'outcome_id.required' => 'Outcome Required',
            'outcome_id.integer' => 'Please specify outcome.',
            'fiscal_year_id.required' => 'Fiscal Year Required',
            'fiscal_year_id.integer' => 'Please specify Fiscal Year.',
        ];
    }
}
