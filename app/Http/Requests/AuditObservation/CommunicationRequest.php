<?php

namespace App\Http\Requests\AuditObservation;

use Illuminate\Foundation\Http\FormRequest;

class CommunicationRequest extends FormRequest
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
            'observation_id' => 'required|numeric',
            'rp_office_id' => 'required|numeric',
            'parent_office_id' => 'required|numeric',
            'directorate_id' => 'required|numeric',
            'message_title' => 'required',
            'message_body' => 'required',
            'sent_to' => 'required',
        ];
    }
}
