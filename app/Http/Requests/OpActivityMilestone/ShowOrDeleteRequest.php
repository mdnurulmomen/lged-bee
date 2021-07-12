<?php

namespace App\Http\Requests\OpActivityMilestone;

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
            'milestone_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'milestone_id.required' => 'Outcome ID Required.',
            'milestone_id.integer' => 'Outcome ID should be integer.'
        ];
    }
}
