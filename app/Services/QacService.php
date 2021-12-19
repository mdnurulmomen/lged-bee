<?php

namespace App\Services;

use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\ApottiStatus;
use App\Models\AuditPlanTeamInfo;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
class QacService
{
    use GenericData, ApiHeart;

    public function qacApotti(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        DB::beginTransaction();

        try {
            ApottiStatus::updateOrCreate(
                ['apotti_id' => $request->apotti_id,'qac_type' => $request->qac_type],
                [
                    'apotti_id' => $request->apotti_id,
                    'apotti_type' => $request->apotti_type,
                    'qac_type' => $request->qac_type,
                    'comment' => $request->comment,
                    'created_by' => $cdesk->office_id,
                    'created_by_name_en' => $cdesk->officer_en,
                    'created_by_name_bn' => $cdesk->officer_bn,
                ]
            );

            Apotti::where('id',$request->apotti_id)->update(['apotti_type' => $request->apotti_type]);
            ApottiItem::where('apotti_id',$request->apotti_id)->update(['memo_type' => $request->apotti_type]);

            DB::commit();
            return ['status' => 'success', 'data' => 'Status Change Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getQacApottiStatus(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }

        try {
//            return ['status' => 'error', 'data' => $request->all()];
           $apotti_status =  ApottiStatus::where('apotti_id',$request->apotti_id)->where('qac_type',$request->qac_type)->first();
            return ['status' => 'success', 'data' => $apotti_status];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }




}
