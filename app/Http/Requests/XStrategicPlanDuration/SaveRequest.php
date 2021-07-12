<?php

namespace App\Http\Requests\XStrategicPlanDuration;

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
            'start_year' => 'integer|required',
            'end_year' => 'integer|required',
            'remarks' => 'string|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'start_year.required' => 'Start Year Required',
            'start_year.integer' => 'Start Year Should Be Year',
            'end_year.required' => 'End Year Required',
            'end_year.integer' => 'End Year Should Be Year',
        ];
    }
}
