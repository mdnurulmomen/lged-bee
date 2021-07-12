<?php

namespace App\Http\Requests\OpActivityMilestone;

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
            'fiscal_year_id' => 'integer|required',
            'outcome_id' => 'integer|required',
            'output_id' => 'integer|required',
            'activity_id' => 'integer|required',
            'title_en' => 'string|required',
            'title_bn' => 'string|required',
            'target_date' => 'date|required',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Activity Milestone ID Required',
            'id.integer' => 'Activity Milestone ID Should Be Number',
            'fiscal_year_id.required' => 'Duration ID Required',
            'fiscal_year_id.integer' => 'Duration ID Should Be Number',
            'outcome_id.required' => 'Outcome ID Required',
            'outcome_id.integer' => 'Outcome ID Should Be Number',
            'output_id.required' => 'Output ID Required',
            'output_id.integer' => 'Output ID Should Be Number',
            'activity_id.required' => 'Activity ID Required',
            'activity_id.integer' => 'Activity ID Should Be Number',
            'target_date.required' => 'Target date is required',
            'target_date.date' => 'Target Date should be in date format.',
            'title_en.required' => 'Activity Title (English) Required',
            'title_en.string' => 'Activity Title (English) Should Be Text',
            'title_bn.required' => 'Activity Title (Bangla) Required',
            'title_bn.string' => 'Activity Title (Bangla) Should Be Text',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->milestone_id,
        ]);
    }
}
