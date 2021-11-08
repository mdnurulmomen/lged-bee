<?php

namespace App\Http\Requests\XRiskAssessment;

use Illuminate\Foundation\Http\FormRequest;

class ShowOrDeleteRequest extends FormRequest
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
            'audit_query_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'audit_query_id.required' => 'Fiscal Year Required.',
            'audit_query_id.integer' => 'Fiscal Year ID should be integer.'
        ];
    }
}
