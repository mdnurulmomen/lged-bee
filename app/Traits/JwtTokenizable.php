<?php

namespace App\Traits;

use Firebase\JWT\JWT;

trait JwtTokenizable
{
    public function generateToken($data_to_encode, $options = [], $secretKey = '', $algorithm = 'HS512'): string
    {
        if (empty($secretKey)) {
            $secretKey = config('bee_config.secret_key');
        }
        $issuedAt = time();
        $tokenId = base64_encode($issuedAt);
        $notBefore = $issuedAt;

        if (array_key_exists('exp', $options)) {
            $expire = $notBefore + (int)$options['exp'];
            unset($options['exp']);
        } else {
            $expire = $notBefore + 3600 * 24 * 1; // Adding 1 day expiration
        }

        $serverName = url('/'); /// set domain name

        /*
         * Create the token as an array
         */
        $data = $options + [
                'iat' => $issuedAt,         // Issued at: time when the token was generated
                'jti' => $tokenId,          // Json Token Id: an unique identifier for the token
                'iss' => $serverName,       // Issuer
                'nbf' => $notBefore,        // Not before
                'exp' => $expire,           // Expire
                'data' => $data_to_encode,
            ];

        /// Here we will transform this array into JWT:
        $token = JWT::encode(
            $data, //Data to be encoded in the JWT
            $secretKey, // The signing key
            $algorithm
        );

        return $token;

    }

    public function checkToken($token)
    {
        if (empty($token)) {
            return responseFormat('error', 'Empty Token');
        }
        $token_data = $this->getTokenData($token);

        if (!isSuccessResponse($token_data)) {
            return responseFormat('error', 'Token response empty');
        } else {
            $token_data = (array)$token_data['data'];
        }

        if (empty($token_data['data'])) {
            return responseFormat('error', 'Token data empty');
        }
        //expiry time check
        if (empty($token_data['exp']) || $token_data['exp'] < time()) {
            return responseFormat('error', 'Token expire');
        }

        // issuer time check
        if (empty($token_data['nbf']) || $token_data['nbf'] > time()) {
            return responseFormat('error', 'Token used before current time');
        }
        return responseFormat('success', $token_data['data']);
    }

    public function getTokenData($token, $secretKey = '', $algorithm = 'HS512')
    {
        try {
            if (empty($secretKey)) {
                $secretKey = config('bee_config.secret_key');
            }
            $payload = JWT::decode($token, $secretKey, array($algorithm));
            $payload->data = (array)$payload->data;
            return ['status' => 'success', 'data' => $payload];
        } catch (\Exception $ex) {
            return false;
        }

    }

    public function getLoginTokenData($data)
    {
        if (!empty($data)) {
            $data_2_unset = [];
            $data_2_change = array_flip($this->getLoginTokenParams());
            foreach ($data as $data_key => $data_val) {
                if (isset($data_2_change[$data_key])) {
                    $value = getDecryptedData($data_val);
                    $data[$data_2_change[$data_key]] = isJson($value) ? (json2Array($value)) : ($value);
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

    public function getLoginTokenParams()
    {
        return [
            'device_id' => 'did',
            'device_type' => 'dt',
            'username' => 'un',
            'employee_record_id' => 'eri',
            'user_id' => 'ui',
            'designations' => 'map',
        ];
    }
}

