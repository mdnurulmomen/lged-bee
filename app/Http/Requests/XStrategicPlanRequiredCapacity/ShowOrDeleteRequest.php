<?php

namespace App\Http\Requests\XStrategicPlanRequiredCapacity;

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
            'required_capacity_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'required_capacity_id.required' => 'Required Capacity ID Required.',
            'required_capacity_id.integer' => 'Required Capacity ID should be integer.'
        ];
    }
}
