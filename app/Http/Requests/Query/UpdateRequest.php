<?php

namespace App\Http\Requests\XFiscalYear;

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
            'start' => 'integer|required',
            'end' => 'integer|required',
            'description' => 'string|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Fiscal Year ID Required',
            'id.integer' => 'Fiscal Year ID Should Be Integer',
            'duration_id.integer' => 'Duration Span Should Be Year',
            'start.required' => 'Start Year Required',
            'start.integer' => 'Start Year Should Be Year',
            'end.required' => 'End Year Required',
            'end.integer' => 'End Year Should Be Year',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'start' => $this->start_year,
            'end' => $this->end_year,
            'id' => $this->fiscal_year_id,
        ]);
    }
}
