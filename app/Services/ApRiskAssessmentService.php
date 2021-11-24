<?php

namespace App\Services;

use App\Http\Controllers\XRiskAssessmentController;
use App\Models\ApRiskAssessment;
use App\Models\ApRiskAssessmentItem;
use App\Models\XRiskAssessment;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApRiskAssessmentService
{
    use GenericData, ApiHeart;

    public function store(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $risk_assessment = new ApRiskAssessment;
            $risk_assessment->fiscal_year_id = $request->fiscal_year_id;
            $risk_assessment->activity_id = $request->activity_id;
            $risk_assessment->audit_plan_id = $request->audit_plan_id;
            $risk_assessment->risk_assessment_type = $request->risk_assessment_type;
            $risk_assessment->total_risk_value = isset($request->total_risk_value)?$request->total_score:null;
            $risk_assessment->risk_rate = isset($request->risk_rate)?$request->risk_rate:null;
            $risk_assessment->risk = isset($request->risk)?$request->risk:null;
            $risk_assessment->created_by = $cdesk->officer_id;
            $risk_assessment->created_by_name_en = $cdesk->officer_en;
            $risk_assessment->created_by_name_bn = $cdesk->officer_bn;
            $risk_assessment->save();
            $lastInsertId = $risk_assessment->id;

            //items
            $riskAssessmentItems = array();
            foreach ($request->risk_assessments as $item) {
                $riskAssessmentItems[] = array(
                    'ap_risk_assessment_id'=> $lastInsertId,
                    'x_risk_assessment_id'=>  $item['risk_assessment_id'],
                    'risk_assessment_title_en'=> $item['risk_assessment_title_en'],
                    'risk_assessment_title_bn'=> $item['risk_assessment_title_bn'],
                    'risk_value'=> $item['risk_value']
                );
            }
            if (!empty($riskAssessmentItems)) {
                ApRiskAssessmentItem::insert($riskAssessmentItems);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'save data successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function update(Request $request)
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        \DB::beginTransaction();
        try {
            $risk_assessment = ApRiskAssessment::find($request->id);
            $risk_assessment->fiscal_year_id = $request->fiscal_year_id;
            $risk_assessment->activity_id = $request->activity_id;
            $risk_assessment->audit_plan_id = $request->audit_plan_id;
            $risk_assessment->risk_assessment_type = $request->risk_assessment_type;
            $risk_assessment->total_risk_value = $request->total_score;
            $risk_assessment->risk_rate = $request->risk_rate;
            $risk_assessment->risk = $request->risk;
            $risk_assessment->updated_by = $cdesk->officer_id;
            $risk_assessment->updated_by_name_en = $cdesk->officer_en;
            $risk_assessment->updated_by_name_bn = $cdesk->officer_bn;
            $risk_assessment->save();

            //delete item
            ApRiskAssessmentItem::where('ap_risk_assessment_id',$request->id)->delete();

            //items
            $riskAssessmentItems = array();
            foreach ($request->risk_assessments as $item) {
                $riskAssessmentItems[] = array(
                    'ap_risk_assessment_id'=> $request->id,
                    'x_risk_assessment_id'=>  $item['risk_assessment_id'],
                    'risk_assessment_title_en'=> $item['risk_assessment_title_en'],
                    'risk_assessment_title_bn'=> $item['risk_assessment_title_bn'],
                    'risk_value'=> $item['risk_value']
                );
            }
            if (!empty($riskAssessmentItems)) {
                ApRiskAssessmentItem::insert($riskAssessmentItems);
            }

            \DB::commit();
            return ['status' => 'success', 'data' => 'update data successfully'];
        } catch (\Exception $exception) {
            \DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function apRiskAssessmentList(Request $request): array
    {

        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {

            $ap_risk_assessment_list = ApRiskAssessment::with(['risk_assessment_items'])
                ->select('id','total_risk_value','risk_rate','risk')
                ->where('risk_assessment_type',$request->risk_assessment_type)
                ->where('fiscal_year_id',$request->fiscal_year_id)
                ->where('audit_plan_id',$request->audit_plan_id)
                ->first();

            return ['status' => 'success', 'data' => $ap_risk_assessment_list];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];

        }

    }

}
