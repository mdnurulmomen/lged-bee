<?php

namespace App\Services;

use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Models\ApottiStatus;
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
//            return ['status' => 'success', 'data' => $request->all()];
            $apotti_status = new ApottiStatus();
            $apotti_status->apotti_id = $request->apotti_id;
            $apotti_status->apotti_type = $request->apotti_type;
            $apotti_status->qac_type = $request->qac_type;
            $apotti_status->comment = $request->comment;
            $apotti_status->created_by = $cdesk->office_id;
            $apotti_status->created_by_name_en = $cdesk->officer_en;
            $apotti_status->created_by_name_bn = $cdesk->officer_bn;
            $apotti_status->save();

            ApottiItem::where('apotti_id',$request->apotti_id)->update(['memo_type' => $request->apotti_type]);

            DB::commit();
            return ['status' => 'success', 'data' => 'Status Change Successfully'];

        } catch (\Exception $exception) {
            DB::rollback();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }




}
