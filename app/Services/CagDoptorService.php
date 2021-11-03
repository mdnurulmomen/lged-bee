<?php

namespace App\Services;

use App\Traits\ApiHeart;
use App\Traits\GenericData;

class CagDoptorService
{
    use GenericData, ApiHeart;

    public function getEmployeeSignature($officer_id)
    {
    }

    public function loadSignatureFromDoptor($username, $employee_ids): array
    {
        $response = $this->initDoptorHttp($username)->post(config('cag_doptor_api.employee_signatures'), ['employee_record_ids' => $employee_ids])->json();
        if (isSuccessResponse($response)) {
            return ['status' => 'success', 'data' => $response['data']];
        } else {
            return ['error' => 'error', 'data' => [], 'message' => 'Signature not found.'];
        }
    }
}
