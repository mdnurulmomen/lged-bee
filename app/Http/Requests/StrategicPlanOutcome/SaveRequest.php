<?php

namespace App\Http\Requests\StrategicPlanOutcome;

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
            'duration_id' => 'integer|required',
            'outcome_no' => 'string|required',
            'outcome_title_en' => 'string|required',
            'outcome_title_bn' => 'string|required',
            'remarks' => 'string|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'duration_id.required' => 'Duration Period Required',
            'duration_id.integer' => 'Duration Period Should Be Year',
            'outcome_no.required' => 'Outcome No Required',
            'outcome_no.string' => 'Outcome No Should Be Year',
            'outcome_title_en.required' => 'Outcome Title (English) Required',
            'outcome_title_en.string' => 'Outcome Title (English) Should Be Text',
            'outcome_title_bn.required' => 'Outcome Title (Bangla) Required',
            'outcome_title_bn.string' => 'Outcome Title (Bangla) Should Be Text',
        ];
    }
}
