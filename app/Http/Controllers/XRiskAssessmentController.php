<?php

namespace App\Http\Controllers;

use App\Http\Requests\XRiskAssessment\SaveRequest;
use App\Http\Requests\XRiskAssessment\ShowOrDeleteRequest;
use App\Http\Requests\XRiskAssessment\UpdateRequest;
use App\Models\XRiskAssessment;
use Illuminate\Http\Request;

class XRiskAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->per_page && $request->page && !$request->all) {
            $risk_assessment = XRiskAssessment::paginate($request->per_page);
        }
        else {
            $fiscal_year_id  = $request->fiscal_year_id;
            $activity_id = $request->activity_id;
            $audit_plan_id  = $request->audit_plan_id;
            $risk_assessment_type = $request->risk_assessment_type;
            $total_risk_value = $request->total_risk_value;
            $risk_rate = $request->risk_rate;
            $risk = $request->risk;

            $query = XRiskAssessment::query();

            $query->when($fiscal_year_id, function ($q, $fiscal_year_id) {
                return $q->where('fiscal_year_id', $fiscal_year_id);
            });

            $query->when($activity_id, function ($q, $activity_id) {
                return $q->where('activity_id', $activity_id);
            });

            $query->when($audit_plan_id, function ($q, $audit_plan_id) {
                return $q->where('audit_plan_id', $audit_plan_id);
            });

            $query->when($risk_assessment_type, function ($q, $risk_assessment_type) {
                return $q->where('risk_assessment_type', $risk_assessment_type);
            });

            $query->when($total_risk_value, function ($q, $total_risk_value) {
                return $q->where('total_risk_value', $total_risk_value);
            });

            $query->when($risk_rate, function ($q, $risk_rate) {
                return $q->where('risk_rate', $risk_rate);
            });

            $query->when($risk, function ($q, $risk) {
                return $q->where('risk', $risk);
            });

            $risk_assessment = $query->get();
        }

        if ($risk_assessment) {
            $response = responseFormat('success', $risk_assessment);
        } else {
            $response = responseFormat('error', 'Risk Assessment Not Found');
        }
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SaveRequest $request): \Illuminate\Http\JsonResponse
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $request->validated();
            foreach ($request->risk_assessment_title_bn as $key => $risk_assessment_title_bn){
                $data['risk_assessment_type'] = $request->risk_assessment_type;
                $data['company_type'] = $request->company_type;
                $data['risk_assessment_title_bn'] = $risk_assessment_title_bn;
                $data['risk_assessment_title_en'] = $request->risk_assessment_title_bn[$key];
                $data['created_by_id'] = $cdesk->officer_id;
                $data['created_by_name'] = $cdesk->officer_bn;
                XRiskAssessment::create($data);
            }

            $response = responseFormat('success', 'Created Successfully');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage(), ['code' => $exception->getCode()]);
        }

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowOrDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $risk_assessment = XRiskAssessment::findOrFail($request->fiscal_year_id);
        if ($risk_assessment) {
            $response = responseFormat('success', $risk_assessment);
        } else {
            $response = responseFormat('error', 'Risk Assessment Not Found');
        }
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        $risk_assessment_id = XRiskAssessment::find($request->id);
        try {
            $risk_assessment_id->update($request->validated());
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Query $Query
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ShowOrDeleteRequest $request)
    {
        try {
            XRiskAssessment::find($request->id)->delete();
            $response = responseFormat('success', 'Successfully Updated');
        } catch (\Exception $exception) {
            $response = responseFormat('error', $exception->getMessage());
        }
        return response()->json($response);
    }
}
