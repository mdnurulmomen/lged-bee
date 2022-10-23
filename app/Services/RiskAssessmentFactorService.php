<?php

namespace App\Services;

use App\Models\RiskAssessmentFactor;
use App\Models\RiskAssessmentFactorItem;
use App\Models\XRiskFactor;
use Illuminate\Http\Request;
use DB;

class RiskAssessmentFactorService
{

    public function list(Request $request): array
    {
        try {
            $type = $request->type;

            $query = RiskAssessmentFactor::query();

            if($type == 'project'){
                $query->whereNotNull('project_id');
            }

            if($type == 'function'){
                $query->whereNotNull('function_id');
            }

            if($type == 'cost_center'){
                $query->whereNotNull('cost_center_id');
            }

            $risk_assessment_factor = $query->where('is_latest',1)->get();
            $risk_assessment = [];

            foreach($risk_assessment_factor as $assessment){
                $data['project_id'] = $assessment->project_id;
                $data['project_name_en'] = $assessment->project_name_en;
                $data['project_name_bn'] = $assessment->project_name_bn;

                $data['function_id'] = $assessment->function_id;
                $data['function_name_bn'] = $assessment->function_name_bn;
                $data['function_name_en'] = $assessment->function_name_en;

                $data['cost_center_id'] = $assessment->cost_center_id;
                $data['cost_center_name_en'] = $assessment->cost_center_name_en;
                $data['cost_center_name_bn'] = $assessment->cost_center_name_bn;

                $data['total_risk_score'] = $assessment->total_risk_score;
                $data['risk_score_key'] = $assessment->risk_score_key;
                $data['risk_factor_items'] = $assessment->risk_factor_items->pluck('factor_rating','x_risk_factor_id');

                $risk_assessment[] = $data;
            }

            return ['status' => 'success', 'data' => $risk_assessment];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function store(Request $request): array
    {
        DB::beginTransaction();

        try {
            $cdesk = json_decode($request->cdesk, false);

            $risk_factor_info = $request->risk_factor_info;

            $query = RiskAssessmentFactor::query();

            if($risk_factor_info['project_id']){
                $query->where('project_id',$risk_factor_info['project_id']);
            }elseif($risk_factor_info['function_id']){
                $query->where('function_id',$risk_factor_info['function_id']);
            }elseif($risk_factor_info['cost_center_id']){
                $query->where('cost_center_id',$risk_factor_info['cost_center_id']);
            }

            $query->update(['is_latest'=> 0]);

            $risk_factor_info['total_risk_score'] = 1.75;
            $risk_factor_info['risk_score_key'] = 'Low';
            $risk_factor_info['is_latest'] = 1;
            $risk_factor_info['created_by'] = $cdesk->officer_id;

            $risk_assessment_id =  RiskAssessmentFactor::insertGetId($risk_factor_info);

            foreach($request->risk_factor_item as $factor){
                $factor['risk_assessment_factor_id'] = $risk_assessment_id;
                $factor['created_by'] = $cdesk->officer_id;
                RiskAssessmentFactorItem::insert($factor);
            }

            DB::commit();
            return ['status' => 'success', 'data' => 'Save Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }
}
