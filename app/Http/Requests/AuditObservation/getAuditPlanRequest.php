<?php

namespace App\Http\Requests\AuditObservation;

use Illuminate\Foundation\Http\FormRequest;

class getAuditPlanRequest extends FormRequest
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
            'office_id' => 'required|numeric',
            'rp_office_id' => 'required|numeric',
            'fiscal_year_id' => 'required|numeric',
        ];
    }
}
