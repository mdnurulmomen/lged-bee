<?php

namespace App\Services;

use App\Models\Apotti;
use App\Models\ApottiItem;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use DB;

class BroadsheetReplyService
{
    use GenericData, ApiHeart;

    public function getApottiItemList(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $apotti_list = ApottiItem::where(function($query){
                    $query->where('memo_type', 'non-sfi')
                        ->orWhere('memo_type', 'sfi');
                })
                ->paginate(config('bee_config.per_page_pagination'));

            return ['status' => 'success', 'data' => $apotti_list];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

    public function getApottiItemInfo(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        $office_db_con_response = $this->switchOffice($cdesk->office_id);
        if (!isSuccessResponse($office_db_con_response)) {
            return ['status' => 'error', 'data' => $office_db_con_response];
        }
        try {
            $apottiIteminfo = ApottiItem::with(['fiscal_year'])->find($request->apotti_item_id);
            return ['status' => 'success', 'data' => $apottiIteminfo];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }

    }

}
