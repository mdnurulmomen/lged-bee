<?php

namespace App\Http\Requests\Query;

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
            'cost_center_type_id' => 'integer|required',
            'query_title_bn' => 'required|string',
            'query_title_en' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Query ID Required',
            'id.integer' => 'Query ID Should Be Integer',
            'cost_center_type_id.integer' => 'Duration Span Should Be Year',
            'query_title_bn.required' => 'Query Titel Bn Required',
            'query_title_en.required' => 'Query Titel En Be Year',
        ];
    }
}
