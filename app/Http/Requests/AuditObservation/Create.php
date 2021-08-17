<?php

namespace App\Http\Requests\AuditObservation;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
            'ministry_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'parent_office_id' => 'required|numeric',
            'rp_office_id' => 'required|numeric',
            'directorate_id' => 'required|numeric',
            'team_leader_id' => 'required|numeric',
            'observation_en' => 'required_without:observation_bn',
            'observation_bn' => 'required_without:observation_en',
            'observation_type' => 'required',
            'amount' => 'required',
            'initiation_date' => 'required',
            'fiscal_year_id' => 'required|numeric',
            'cover_page' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,doc,docx|max:10420',
            'main_attachments.*' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,doc,docx|max:10420',
            'appendix_attachments.*' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,doc,docx|max:10420',
            'authentic_attachments.*' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,doc,docx|max:10420',
            'other_attachments.*' => 'nullable|mimes:jpeg,jpg,png,gif,pdf,doc,docx|max:10420',
        ];
    }
}
