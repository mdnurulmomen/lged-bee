<?php

namespace App\Http\Controllers;

use App\Services\CagDoptorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    public function loginInAmms(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if (empty($request->hasHeader('device-id')) || empty($request->hasHeader('device-type')) || empty($request->user_data)) {
                $response = responseFormat('error', __('Missing required information'), ['code' => MISSING_DEVICE_INFO_CODE]);
                return response()->json($response, UNAUTHORIZED_CODE);
            }
            $data['device_id'] = $request->header('device-id');
            $data['device_type'] = $request->header('device-type');

            $user_data = json_decode(gzuncompress(base64_decode($request->user_data)), true);
            $data['user_data'] = $user_data['user_info'];

            if ($user_data['user_info']['user']['user_role_id'] == 1) {
                $response['status'] = 'success';
                $response['data'] = $data['user_data'];
            } else {
                $response = $this->setOfficeDomains($data['user_data']);
                if (!isSuccessResponse($response)) {
                    throw new \Exception($response['message']);
                }
            }

            $token_response = $this->makeCagToken($response['data'] + [
                    'device_id' => $data['device_id'],
                    'device_type' => $data['device_type'],
                ]);
            if (!isSuccessResponse($token_response)) {
                throw new \Exception('Token Generation Error');
            }
            $response['data']['token'] = $token_response['data'];

//            get user signature
            //$emp_signature = (new CagDoptorService())->loadSignatureFromDoptor($user_data['user_info']['user']['username'], $user_data['user_info']['user']['employee_record_id']);
            //$response['data']['signature'] = $emp_signature['data'][0]['signature'];

//            $ta_dds = $gd->getModel('DoptorDataSettings');
//            $dds = $ta_dds->getAll(['office_sync'], ['id' => 1])->enableHydration(false)->first();
//            if (!empty($dds)) {
//                $response['data']['office_sync'] = $dds['office_sync'];
//            } else {
//                $response['data']['office_sync'] = 0;
//            }

            return response()->json($response);

        } catch (\Exception $ex) {
            return response()->json(responseFormat('error', __('Technical Error Happen. Error: LIA'), ['details' => $ex->getMessage(), 'code' => $ex->getCode()]), 500);
        }
    }

    protected function setOfficeDomains($data): array
    {
        try {
            $office_infos = $data['office_info'];
            $office_ids = [];
            foreach ($office_infos as $office_info) {
                $office_ids[] = $office_info['office_id'];
            }
            if (count($office_ids) > 0) {
                $domains_information = (new \App\Models\OfficeDomain)->getOfficeDomains($office_ids);
                if ($domains_information)
                    foreach ($office_infos as $key => &$office_info) {
                        foreach ($domains_information as $domain_information) {
                            if ($domain_information['office_id'] == $office_info['office_id']) {
                                $office_info['office_domain_url'] = $domain_information['domain_url'];
                            }
                        }
                        $data['office_info'] = $office_infos;
                    }
                return responseFormat('success', $data);
            } else {
                $msg = __("অফিস ডাটাবেজ পাওয়া যায় নি! সাপোর্ট টিমের সাথে যোগাযোগ করুন।");
                throw new \Exception($msg);
            }
        } catch (\Exception $ex) {
            return responseFormat('error', __('Technical Error happen. Error: SOD'), ['details' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    protected function makeCagToken($data): array
    {
        try {
            $designations = [];
            if (!isset($data['office_info'])) {
                $data['office_info'] = [];
            }
            foreach ($data['office_info'] as $office_info) {
                $designations[$office_info['office_unit_organogram_id']]['office_id'] = $office_info['office_id'];
                $designations[$office_info['office_unit_organogram_id']]['office_unit_id'] = $office_info['office_unit_id'];
                $designations[$office_info['office_unit_organogram_id']]['office_head'] = $office_info['office_head'];
            }
            $token_data = $this->setLoginTokenData([
                'device_id' => $data['device_id'],
                'device_type' => $data['device_type'],
                'username' => $data['user']['username'],
                'employee_record_id' => $data['user']['employee_record_id'],
                'user_id' => $data['user']['id'],
                'designations' => $designations,
            ]);
            $token_response = $this->generateToken($token_data);
            return ['status' => 'success', 'data' => $token_response];
        } catch (\Exception $ex) {
            return responseFormat(
                'error',
                __('Technical Error Happen. Error: MCT'), ['details' => $ex->getMessage(), 'code' => $ex->getCode()]);
        }
    }

    protected function setLoginTokenData($data)
    {
        if (!empty($data)) {
            $data_2_unset = [];
            $data_2_change = $this->getLoginTokenParams();
            foreach ($data as $data_key => $data_val) {
                if (isset($data_2_change[$data_key])) {
                    $data[$data_2_change[$data_key]] = is_array($data_val) ? makeEncryptedData(json_encode($data_val)) : makeEncryptedData($data_val);
                    $data_2_unset[] = $data_key;
                }
            }
            if (!empty($data_2_unset)) {
                foreach ($data_2_unset as $unset) {
                    unset($data[$unset]);
                }
            }
        }
        return $data;
    }

    public function clientLogin(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            Validator::make($request->all(), [
                'client_id' => 'required',
                'password' => 'required',
            ])->validate();

            if (!($request->client_id == config('bee_config.client_id') && $request->password == config('bee_config.client_pass'))) {
                throw new \Exception('Client ID or Client Password is not matching. Please provide valid credentials.');
            }

            $token_data = [
                'client_id' => $request->client_id,
                'client_password' => $request->password,
            ];

            $token_response = $this->generateToken($token_data);
            $response = ['status' => 'success'];
            $response['data']['token'] = $token_response;
            return response()->json($response);
        } catch (\Exception $ex) {
            return response()->json(responseFormat('error', __('Technical Error Happen. Error.'), ['details' => $ex->getMessage(), 'code' => $ex->getCode()]), 500);
        }
    }
}
