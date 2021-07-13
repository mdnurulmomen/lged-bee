<?php

namespace App\Http\Requests\OperationalPlan;

use Illuminate\Foundation\Http\FormRequest;

class Operational extends FormRequest
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

    public function rules()
    {
        return [
            'fiscal_year_id' => 'integer|required',
        ];
    }

    public function messages(): array
    {
        return [
            'fiscal_year_id.required' => 'Fiscal Year Id Required',
            'fiscal_year_id.integer' => 'Fiscal Year Id Should Be Number',
        ];
    }
}
