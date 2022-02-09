<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait ApiHeart
{
    public function initDoptorHttp($username): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withoutVerifying()->withHeaders($this->apiHeaders())->withToken($this->getDoptorToken($username));
    }

    public function apiHeaders(): array
    {
        return ['Accept' => 'application/json', 'Content-Type' => 'application/json; charset=utf-8', 'api-version' => '1'];
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

    public function initHttp(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withoutVerifying()->withHeaders($this->apiHeaders());
    }

    public function initRPUHttp(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withoutVerifying()->withHeaders($this->apiHeaders())->withToken($this->getRPUniverseToken());
    }

    public function getRPUniverseToken()
    {
        $url = config('cag_rpu_api.auth.client_login_url');
        $client_id = config('cag_rpu_api.auth.client_id');
        $client_pass = config('cag_rpu_api.auth.client_pass');
        if (!session()->has('_rpu_token') || session('_rpu_token') == '') {
            $token = $this->getClientToken($url, $client_id, $client_pass);
            session(['_rpu_token' => $token]);
        }
        return session('_rpu_token');
    }

    public function initPonjikaHttp(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withoutVerifying()->withHeaders($this->apiHeaders())->withToken($this->getPonjikaToken());
    }

    public function getPonjikaToken()
    {
        $url = config('cag_ponjika_api.auth.client_login_url');
        $client_id = config('cag_ponjika_api.auth.client_id');
        $client_pass = config('cag_ponjika_api.auth.client_pass');
        if (!session()->has('_ponjika_token') || session('_ponjika_token') == '') {
            $token = $this->getClientToken($url, $client_id, $client_pass);
            session(['_ponjika_token' => $token]);
        }
        return session('_ponjika_token');
    }

}

