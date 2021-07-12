<?php

namespace App\Http\Requests\XStrategicPlanOutput;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'id' => 'integer|required',
            'duration_id' => 'integer|required',
            'outcome_id' => 'integer|required',
            'output_no' => 'string|required',
            'output_title_en' => 'string|required',
            'output_title_bn' => 'string|required',
            'remarks' => 'string|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Output ID Required',
            'id.integer' => 'Output ID Should Be Number',
            'duration_id.required' => 'Duration ID Required',
            'duration_id.integer' => 'Duration ID Should Be Number',
            'outcome_id.required' => 'Outcome ID Required',
            'outcome_id.integer' => 'Outcome ID Should Be Year',
            'output_no.required' => 'Output No Required',
            'output_no.string' => 'Output No Should Be Year',
            'output_title_en.required' => 'Output Title (English) Required',
            'output_title_en.string' => 'Output Title (English) Should Be Text',
            'output_title_bn.required' => 'Output Title (Bangla) Required',
            'output_title_bn.string' => 'Output Title (Bangla) Should Be Text',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->output_id,
        ]);
    }
}
