<?php

namespace App\Http\Requests\Query;

use Illuminate\Foundation\Http\FormRequest;

class SaveRequest extends FormRequest
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
            'cost_center_type_id' => 'required',
            'query_title_bn' => 'required|array',
            'query_title_en' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'cost_center_type_id.required' => 'Cost Center Type Required',
            'query_title_bn.required' => 'Query Title Bangla Required',
            'query_title_en.required' => 'Query Title English Required',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'start' => $this->start_year,
            'end' => $this->end_year,
        ]);
    }
}
