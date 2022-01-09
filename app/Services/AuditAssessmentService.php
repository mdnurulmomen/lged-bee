<?php

namespace App\Services;

use App\Models\AuditAssessmentScore;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditAssessmentService
{
    use GenericData, ApiHeart;

    public function store(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        \DB::beginTransaction();

        try {
            foreach ($request->audit_assessment_score_ids as $key => $score_id){
                $auditAssessmentScore = AuditAssessmentScore::find($score_id);
                $auditAssessmentScore->is_first_half = $request->first_half_data[$key];
                $auditAssessmentScore->is_second_half = $request->second_half_data[$key];
                $auditAssessmentScore->save();
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'Saved Successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }


    public function list(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $responseData = AuditAssessmentScore::with(['fiscal_year'])
                ->addSelect(['total_score' => function ($query) {
                    $query->select(\DB::raw('sum(score)'))
                        ->from('audit_assessment_score_items')
                        ->whereColumn('audit_assessment_score_id', 'audit_assessment_scores.id')
                        ->groupBy('audit_assessment_score_id');
                }])
                ->where('fiscal_year_id',$request->fiscal_year_id)
                ->get();
            return ['status' => 'success', 'data' => $responseData];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getAssessmentEntity(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $ministry_id = $request->office_ministry_id;
            $office_category_id = $request->office_category_type;
            $activity_id = $request->activity_id;
            $query = AuditAssessmentScore::query();

            $query->when($ministry_id, function ($q, $ministry_id) {
                return $q->where('ministry_id', $ministry_id);
            });

            $query->when($office_category_id, function ($q, $office_category_id) {
                return $q->where('category_id', $office_category_id);
            });

            if($activity_id == 7){
                $query->where('is_first_half',1);
            }else{
                $query->where('is_second_half',1);
            }

            $responseData = $query->where('fiscal_year_id',$request->fiscal_year_id)->get();

            return ['status' => 'success', 'data' => $responseData];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
