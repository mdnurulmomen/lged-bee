<?php

namespace App\Http\Requests\OpActivity;

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
            'duration_id' => 'integer|required',
            'outcome_id' => 'integer|required',
            'output_id' => 'integer|required',
            'activity_no' => 'string|required',
            'title_en' => 'string|required',
            'title_bn' => 'string|required',
            'activity_type' => 'required',
            'activity_parent_id' => 'integer|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Activity ID Required',
            'id.integer' => 'Activity ID Should Be Number',
            'duration_id.required' => 'Duration ID Required',
            'duration_id.integer' => 'Duration ID Should Be Number',
            'outcome_id.required' => 'Outcome ID Required',
            'outcome_id.integer' => 'Outcome ID Should Be Number',
            'output_id.required' => 'Output ID Required',
            'output_id.integer' => 'Output ID Should Be Number',
            'activity_no.required' => 'Activity No Required',
            'activity_no.string' => 'Activity No Should Be Year',
            'title_en.required' => 'Activity Title (English) Required',
            'title_en.string' => 'Activity Title (English) Should Be Text',
            'title_bn.required' => 'Activity Title (Bangla) Required',
            'title_bn.string' => 'Activity Title (Bangla) Should Be Text',
            'activity_type.required' => 'Activity Type is required',
            'activity_parent_id.integer' => 'Activity Parent ID Should Be Number',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->activity_id,
        ]);
    }
}
