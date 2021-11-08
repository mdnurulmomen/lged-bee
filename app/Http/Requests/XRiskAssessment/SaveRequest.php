<?php

namespace App\Http\Requests\XRiskAssessment;

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
            'risk_assessment_type' => 'required',
            'company_type' => 'nullable',
            'risk_assessment_title_bn.*' => 'required',
            'risk_assessment_title_en.*' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'risk_assessment_type.required' => 'Risk Assessment Type Required',
            'risk_assessment_title_en.*.required' => 'Risk Assessment Title Bangla Required',
            'risk_assessment_title_bn.*.required' => 'Risk Assessment Title English Required',
        ];
    }
}
