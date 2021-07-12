<?php

namespace App\Http\Requests\OpActivityMilestone;

use Illuminate\Foundation\Http\FormRequest;

class SaveRequest extends FormRequest
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

    public function rules()
    {
        return [
            'fiscal_year_id' => 'integer|required',
            'outcome_id' => 'integer|required',
            'output_id' => 'integer|required',
            'activity_id' => 'integer|required',
            'title_en' => 'string|required',
            'title_bn' => 'string|required',
            'target_date' => 'date|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'fiscal_year_id.required' => 'Duration ID Required',
            'fiscal_year_id.integer' => 'Duration ID Should Be Number',
            'outcome_id.required' => 'Outcome ID Required',
            'outcome_id.integer' => 'Outcome ID Should Be Number',
            'output_id.required' => 'Output ID Required',
            'output_id.integer' => 'Output ID Should Be Number',
            'activity_id.required' => 'Activity ID Required',
            'activity_id.integer' => 'Activity ID Should Be Number',
            'title_en.required' => 'Activity Title (English) Required',
            'title_en.string' => 'Activity Title (English) Should Be Text',
            'title_bn.required' => 'Activity Title (Bangla) Required',
            'title_bn.string' => 'Activity Title (Bangla) Should Be Text',
        ];
    }

}
