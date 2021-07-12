<?php

namespace App\Http\Requests\XStrategicPlanRequiredCapacity;

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
            'capacity_no' => 'string|required',
            'title_en' => 'string|required',
            'title_bn' => 'string|required',
            'remarks' => 'string|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Capacity ID Required',
            'id.integer' => 'Capacity ID Should Be Number',
            'duration_id.required' => 'Duration ID Required',
            'duration_id.integer' => 'Duration ID Should Be Number',
            'outcome_id.required' => 'Outcome ID Required',
            'outcome_id.integer' => 'Outcome ID Should Be Year',
            'capacity_no.required' => 'Capacity No Required',
            'capacity_no.string' => 'Capacity No Should Be Year',
            'title_en.required' => 'Capacity Title (English) Required',
            'title_en.string' => 'Capacity Title (English) Should Be Text',
            'title_bn.required' => 'Capacity Title (Bangla) Required',
            'title_bn.string' => 'Capacity Title (Bangla) Should Be Text',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->required_capacity_id,
        ]);
    }
}
