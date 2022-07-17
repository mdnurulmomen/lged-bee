<?php

namespace App\Services;

use App\Models\AnnualPlan;
use App\Models\AnnualPlanEntitie;
use App\Models\AnnualPlanMain;
use App\Models\AuditAssessmentScore;
use App\Models\OpActivity;
use App\Models\OpOrganizationYearlyAuditCalendarEventSchedule;
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
            foreach ($request->audit_assessment_score_ids as $key => $score_id) {
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
                ->where('fiscal_year_id', $request->fiscal_year_id)
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

            if ($activity_id == 7) {
                $query->where('is_first_half', 1);
            } else {
                $query->where('is_second_half', 1);
            }

            $responseData = $query->where('fiscal_year_id', $request->fiscal_year_id)->get();

            return ['status' => 'success', 'data' => $responseData];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function storeAnnualPlan(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        \DB::beginTransaction();

        try {

            //            return ['status' => 'error', 'data' => $request->all()];
            $opActivity = OpActivity::where('fiscal_year_id', $request->fiscal_year_id)
                ->where('activity_type', $request->compliance)
                ->get();

            $op_audit_calendar_event_id = OpOrganizationYearlyAuditCalendarEventSchedule::select('op_audit_calendar_event_id')->where('fiscal_year_id', $request->fiscal_year_id)->first()->op_audit_calendar_event_id;
            $nominated_man_powers = [
                'comment' => '',
                'nominated_man_power_counts' => 0,
                'staffs' => [],
            ];
            //for items
            $annualPlanEntityList = [];
            //            return ['status' => 'success', 'data' => $annual_plan_main;

            foreach ($request->audit_assessment_score_ids as $key => $score_id) {
                //for first half
                if ($request->first_half_data[$key] == 1 && $request->has_first_half_annual_plans[$key] == 0) {
                    $auditAssessmentScore = AuditAssessmentScore::find($score_id);
                    $auditAssessmentScore->is_first_half = $request->first_half_data[$key];
                    $auditAssessmentScore->has_first_half_annual_plan = 1;
                    $auditAssessmentScore->save();

                    $annualPlanData = [
                        'schedule_id' => 0,
                        'milestone_id' => 0,
                        'activity_id' => 7,
                        'fiscal_year_id' => $request->fiscal_year_id,
                        'op_audit_calendar_event_id' => $op_audit_calendar_event_id,
                        'annual_plan_type' => 'entity_based',
                        'office_type' => $request->bn_category_titles[$key],
                        'office_type_id' => $request->category_ids[$key],
                        'office_type_en' => $request->en_category_titles[$key],
                        'nominated_man_powers' => json_encode($nominated_man_powers, JSON_UNESCAPED_UNICODE),
                        'created_by' => $cdesk->officer_id,
                    ];

                    $annual_plan_main = AnnualPlanMain::where('fiscal_year_id', $request->fiscal_year_id)
                        ->where('op_audit_calendar_event_id', $op_audit_calendar_event_id)
                        ->first();

                    if ($annual_plan_main) {
                        $annualPlanData['annual_plan_main_id'] = $annual_plan_main->id;
                    } else {
                        $main_plan = new AnnualPlanMain();
                        $main_plan->fiscal_year_id = $request->fiscal_year_id;
                        $main_plan->op_audit_calendar_event_id = $op_audit_calendar_event_id;
                        $main_plan->activity_type = 'compliance';
                        $main_plan->approval_status = 'draft';
                        $main_plan->save();
                        $annualPlanData['annual_plan_main_id'] = $main_plan->id;
                    }

                    $annualPlan = AnnualPlan::create($annualPlanData);

                    array_push(
                        $annualPlanEntityList,
                        array(
                            'annual_plan_id' => $annualPlan->id,
                            'ministry_id' => $request->ministry_ids[$key],
                            'ministry_name_bn' => $request->bn_ministry_names[$key],
                            'ministry_name_en' => $request->en_ministry_names[$key],
                            'entity_id' => $request->entity_ids[$key],
                            'entity_name_bn' => $request->bn_entity_names[$key],
                            'entity_name_en' => $request->bn_entity_names[$key],
                            'entity_name_en' => $request->bn_entity_names[$key],
                            'nominated_offices' =>   json_encode([])
                        )
                    );
                }

                //for second half
                if ($request->second_half_data[$key] == 1 && $request->has_second_half_annual_plans[$key] == 0) {
                    $auditAssessmentScore = AuditAssessmentScore::find($score_id);
                    $auditAssessmentScore->is_second_half = $request->second_half_data[$key];
                    $auditAssessmentScore->has_second_half_annual_plan = 1;
                    $auditAssessmentScore->save();

                    $annualPlanData = [
                        'schedule_id' => 0,
                        'milestone_id' => 0,
                        'activity_id' => 8,
                        'fiscal_year_id' => $request->fiscal_year_id,
                        'op_audit_calendar_event_id' => $op_audit_calendar_event_id,
                        'annual_plan_type' => 'entity_based',
                        'office_type' => $request->bn_category_titles[$key],
                        'office_type_id' => $request->category_ids[$key],
                        'office_type_en' => $request->en_category_titles[$key],
                        'nominated_man_powers' => json_encode($nominated_man_powers, JSON_UNESCAPED_UNICODE),
                        'created_by' => $cdesk->officer_id,
                    ];

                    $annual_plan_main = AnnualPlanMain::where('fiscal_year_id', $request->fiscal_year_id)
                        ->where('op_audit_calendar_event_id', $op_audit_calendar_event_id)
                        ->first();

                    if ($annual_plan_main) {
                        $annualPlanData['annual_plan_main_id'] = $annual_plan_main->id;
                    } else {
                        $main_plan = new AnnualPlanMain();
                        $main_plan->fiscal_year_id = $request->fiscal_year_id;
                        $main_plan->op_audit_calendar_event_id = $op_audit_calendar_event_id;
                        $main_plan->activity_type = 'compliance';
                        $main_plan->approval_status = 'draft';
                        $main_plan->save();
                        $annualPlanData['annual_plan_main_id'] = $main_plan->id;
                    }

                    $annualPlan = AnnualPlan::create($annualPlanData);

                    array_push(
                        $annualPlanEntityList,
                        array(
                            'annual_plan_id' => $annualPlan->id,
                            'ministry_id' => $request->ministry_ids[$key],
                            'ministry_name_bn' => $request->bn_ministry_names[$key],
                            'ministry_name_en' => $request->en_ministry_names[$key],
                            'entity_id' => $request->entity_ids[$key],
                            'entity_name_bn' => $request->bn_entity_names[$key],
                            'entity_name_en' => $request->bn_entity_names[$key],
                            'nominated_offices' =>   json_encode([])
                        )
                    );
                }
            }

            if (!empty($annualPlanEntityList)) {
                AnnualPlanEntitie::insert($annualPlanEntityList);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'Saved Successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
