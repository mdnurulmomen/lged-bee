<?php

namespace App\Http\Requests\OpActivityMilestone;

use Illuminate\Foundation\Http\FormRequest;

class ActivityMilestoneRequest extends FormRequest
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
            'duration_id' => 'integer|required',
        ];
    }

    public function messages()
    {
        return [
            'duration_id.required' => 'Strategic Plan Duration Is Required'
        ];
    }
}
