<?php

namespace App\Services;
use App\Models\AcMemo;
use App\Models\ApEntityIndividualAuditPlan;
use App\Models\Apotti;
use App\Models\ApottiRAirMap;
use App\Models\RAir;
use App\Models\XFiscalYear;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RpuAirReportService
{
    use GenericData, ApiHeart;

    public function airSendToRpu(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
//        return ['status' => 'success', 'data' => $cdesk];
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $air_info = RAir::where('id',$request->air_id)->first();
            $fiscal_year = XFiscalYear::find($air_info->fiscal_year_id);

//            $apotti_list = ApottiRAirMap::where('rairs_id',$request->air_id)->get();

//            foreach ($apotti_list as $apotti){
//                foreach ($apotti->apotti_map_list as $apotti_item){
//                    return ['status' => 'success', 'data' => $apotti_item['id']];
//                }
//            }


            $data['fiscal_year_id'] = $air_info->fiscal_year_id;
            $data['cost_center_id'] = $air_info->entity_id;
            $data['fiscal_year'] = $fiscal_year->start.'-'.$fiscal_year->end;
            $data['annual_plan_id'] = $air_info->annual_plan_id;
            $data['audit_plan_id'] = $air_info->audit_plan_id;
            $data['activity_id'] = $air_info->activity_id;
            $data['air_description'] = gzuncompress(getDecryptedData($air_info->air_description));
            $data['directorate_id'] = $cdesk->office_id;
            $data['directorate_en'] = $cdesk->office_name_en;
            $data['directorate_bn'] = $cdesk->office_name_bn;
            $data['sender_id'] = $cdesk->officer_id;
            $data['sender_en'] = $cdesk->officer_en;
            $data['sender_bn'] = $cdesk->officer_bn;
            $data['send_date'] = date('Y-m-d');

//            return ['status' => 'success', 'data' => $data];

            $send_air_to_rpu = $this->initRPUHttp()->post(config('cag_rpu_api.send_air_to_rpu'), $data)->json();
//            return ['status' => 'success', 'data' => $send_air_to_rpu];
            if ($send_air_to_rpu['status'] == 'success') {
                return ['status' => 'success', 'data' => 'Air Send Successfully'];
            }
//            return ['status' => 'success', 'data' => $air_info];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }



    }
}
