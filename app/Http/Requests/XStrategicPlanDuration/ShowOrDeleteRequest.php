<?php

namespace App\Http\Requests\XStrategicPlanDuration;

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
            'duration_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'duration_id.required' => 'Duration ID Required.',
            'duration_id.integer' => 'Duration ID should be integer.'
        ];
    }
}
