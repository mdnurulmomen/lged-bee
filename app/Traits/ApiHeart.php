<?php

namespace App\Traits;

trait ApiHeart
{
    public function initDoptorHttp(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders($this->apiHeaders())->withToken($this->getDoptorToken($this->getUsername()));
    }

    public function getDoptorToken($username)
    {
        $url = config('cag_doptor_api.auth.client_login_url');
        $client_id = config('cag_doptor_api.auth.client_id');
        $client_pass = config('cag_doptor_api.auth.client_pass');

        if (!session()->has('_doptor_token') || session('_doptor_token') == '') {
            $token = $this->getClientToken($url, $client_id, $client_pass, $username);
            session(['_doptor_token' => $token]);
        }
        return session('_doptor_token');
    }

    public function getClientToken($url, $client_id, $client_pass, $username = '')
    {
        if ($username == '') {
            $getToken = $this->initHttp()->post($url, ['client_id' => $client_id, 'password' => $client_pass]);
        } else {
            $getToken = $this->initHttp()->post($url, ['client_id' => $client_id, 'password' => $client_pass, 'username' => $username,]);
        }

        if ($getToken->status() == 200 && $getToken->json()['status'] == 'success') {
            return $getToken->json()['data']['token'];
        } else {
            return '';
        }
    }
}

