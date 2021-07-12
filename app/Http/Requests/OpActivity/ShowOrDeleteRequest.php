<?php

namespace App\Http\Requests\OpActivity;

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
            'activity_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'activity_id.required' => 'Activity ID Required.',
            'activity_id.integer' => 'Activity ID should be integer.'
        ];
    }
}
