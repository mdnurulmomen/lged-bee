<?php

namespace App\Services;

use App\Models\AuditTemplate;
use App\Traits\ApiHeart;
use App\Traits\GenericData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QCService
{
    use GenericData, ApiHeart;

    public function createNewAIRReport(Request $request): array
    {
        $cdesk = json_decode($request->cdesk, false);
        try {
            $office_db_con_response = $this->switchOffice($cdesk->office_id);
            if (!isSuccessResponse($office_db_con_response)) {
                return ['status' => 'error', 'data' => $office_db_con_response];
            }

            $auditTemplate = AuditTemplate::where('template_type', $request->template_type)
                ->where('lang', 'bn')->first()->toArray();

            return ['status' => 'success', 'data' => $auditTemplate];
        } catch (\Exception $exception) {
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }
}
