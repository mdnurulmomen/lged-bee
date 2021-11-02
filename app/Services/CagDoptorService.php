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

    public function loadSignatureFromDoptor($username, $employee_id): array
    {
        $response = $this->initDoptorHttp($username)->post(config('cag_doptor_api.employee_signature'), ['username' => $username])->json();
        if (isSuccessResponse($response)) {
            return ['status' => 'success', 'data' => $response['data']['encode_sign']];
        } else {
            return ['error' => 'error', 'data' => [], 'message' => 'Signature not found.'];
        }
    }
}
