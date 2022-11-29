<?php

namespace App\Services;

use App\Models\RiskAssessmentFactor;
use App\Models\RiskAssessmentFactorItem;
use App\Models\XRiskFactor;
use Illuminate\Http\Request;
use DB;
use App\Traits\ApiHeart;
use App\Models\XRiskLevel;

class RiskAssessmentFactorService
{
    use ApiHeart;

    public function list(Request $request): array
    {
        try {
            $type = $request->type;

            $query = RiskAssessmentFactor::query();

            if($type == 'project'){
                $query->where('item_type', 'project');
            }

            if($type == 'function'){
                $query->where('item_type', 'function');
            }

            if($type == 'cost_center'){
                $query->where('item_type', 'cost_center');
            }

            $risk_assessment_factor = $query->where('is_latest',1)->get();
            $risk_assessment = [];

            foreach($risk_assessment_factor as $assessment){
                $data['item_id'] = $assessment->item_id;
                $data['item_name_en'] = $assessment->item_name_en;
                $data['item_name_bn'] = $assessment->item_name_bn;
                $data['item_type'] = $assessment->item_type;
                $data['total_risk_score'] = $assessment->total_risk_score;
                $data['risk_score_key'] = $assessment->risk_score_key;
                $data['risk_factor_items'] = $assessment->risk_factor_items;

                $risk_assessment[] = $data;
            }

            return ['status' => 'success', 'data' => $risk_assessment];

        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function store(Request $request): array
    {
        // return ['status' => 'success', 'data' => is_file($request->risk_factor_items[0]['attachment'])];

        DB::beginTransaction();

        try {
            $cdesk = json_decode($request->cdesk, false);

            $risk_factor_info = $request->risk_factor_info;

            $updateLatestOne = RiskAssessmentFactor::where('item_id', $risk_factor_info['item_id'])
            ->where('item_type', $risk_factor_info['item_type'])
            ->update(['is_latest'=> 0]);

            $score = $this->calculateRiskScore($request->risk_factor_items);

            $risk_factor_info['total_risk_score'] = $score;
            $risk_factor_info['risk_score_key'] = $this->getRiskLevel($score);
            $risk_factor_info['is_latest'] = 1;
            $risk_factor_info['created_by'] = $cdesk->officer_id;

            $risk_assessment_id =  RiskAssessmentFactor::insertGetId($risk_factor_info);

            foreach($request->risk_factor_items as $factorIndex => $factor){

                if(is_file($factor['attachment'])) {

                    // File Object
                    $file = $factor['attachment'];

                    // File extension
                    $extension = $file->getClientOriginalExtension();

                    $filename = $risk_assessment_id.'-'.$factorIndex.".".$extension;

                    // File upload location
                    $location = 'public/factor-risk-assessment';

                    // Upload file
                    $file->storeAs($location, $filename);

                    // File path
                    $filepath = asset('storage/factor-risk-assessment/'.$filename);
                }

                $factor['risk_assessment_factor_id'] = $risk_assessment_id;
                $factor['attachment'] = $filepath ?? NULL;
                $factor['created_by'] = $cdesk->officer_id;

                RiskAssessmentFactorItem::insert($factor);
            }

            DB::commit();

            $data = ['id' => $risk_factor_info['item_id'], 'total_risk_score' => $risk_factor_info['total_risk_score'], 'risk_score_key' => $risk_factor_info['risk_score_key']];

            return ['status' => 'success', 'data' => $data];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    private function calculateRiskScore($risk_factor_items)
    {
        $sum = 0;

        foreach (json_decode(json_encode($risk_factor_items)) as $factorItem) {

            $sum += (($factorItem->factor_weight * $factorItem->factor_rating) / 100);

        }

        return $sum;
    }

    private function getRiskLevel($score)
    {
        return XRiskLevel::where('level_from', '<=', $score)->where('level_to', '>=', $score)->first()->title_en ?? '--';
    }
}
