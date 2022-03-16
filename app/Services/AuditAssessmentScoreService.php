<?php

namespace App\Services;

use App\Models\AuditAssessmentScore;
use App\Models\AuditAssessmentScoreItem;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditAssessmentScoreService
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
            $auditAssessmentScore = new AuditAssessmentScore();
            $auditAssessmentScore->category_id = $request->category_id;
            $auditAssessmentScore->category_title_en = $request->category_title_en;
            $auditAssessmentScore->category_title_bn = $request->category_title_bn;
            $auditAssessmentScore->fiscal_year_id = $request->fiscal_year_id;
            $auditAssessmentScore->ministry_id = $request->ministry_id;
            $auditAssessmentScore->ministry_name_en = $request->ministry_name_en;
            $auditAssessmentScore->ministry_name_bn = $request->ministry_name_bn;
            $auditAssessmentScore->entity_id = $request->entity_id;
            $auditAssessmentScore->entity_name_bn = $request->entity_name_bn;
            $auditAssessmentScore->entity_name_en = $request->entity_name_en;
            $auditAssessmentScore->point = $request->point;
            $auditAssessmentScore->last_audit_year_start = empty($request->last_audit_year_start)?null:$request->last_audit_year_start;
            $auditAssessmentScore->last_audit_year_end = empty($request->last_audit_year_end)?null:$request->last_audit_year_end;
            $auditAssessmentScore->created_by = $cdesk->officer_id;
            $auditAssessmentScore->updated_by = $cdesk->officer_id;
            $auditAssessmentScore->save();

            //for items
            $finalItems = [];
            foreach ($request->criteria_ids as $key => $criteria_id){
                if (!empty($request->scores[$key])){
                    array_push($finalItems, array(
                            'audit_assessment_score_id' => $auditAssessmentScore->id,
                            'criteria_id' => $criteria_id,
                            'value' =>  $request->values[$key],
                            'score' =>  $request->scores[$key]
                        )
                    );
                }
            }

            if (!empty($finalItems)){
                AuditAssessmentScoreItem::insert($finalItems);
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
                ->orderBy('id','DESC')
                ->paginate(config('bee_config.per_page_pagination'));
            return ['status' => 'success', 'data' => $responseData];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function edit(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $responseData = AuditAssessmentScore::with(['fiscal_year','audit_assessment_score_items.criteria'])
                ->addSelect(['total_score' => function ($query) {
                    $query->select(\DB::raw('sum(score)'))
                        ->from('audit_assessment_score_items')
                        ->whereColumn('audit_assessment_score_id', 'audit_assessment_scores.id')
                        ->groupBy('audit_assessment_score_id');
                }])
                ->where('id',$request->audit_assessment_score_id)
                ->first();
            return ['status' => 'success', 'data' => $responseData];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
