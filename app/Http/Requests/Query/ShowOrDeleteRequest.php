<?php

namespace App\Http\Requests\XFiscalYear;

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
            'fiscal_year_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'fiscal_year_id.required' => 'Fiscal Year Required.',
            'fiscal_year_id.integer' => 'Fiscal Year ID should be integer.'
        ];
    }
}
