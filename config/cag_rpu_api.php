<?php
return [
    'auth' => [
        'client_login_url' => env('API_URL_RPU', '') . '/client-login',
        'client_id' => env('RPU_CLIENT_ID', ''),
        'client_pass' => env('RPU_CLIENT_PASS', ''),
    ],
    'get-office-ministry-list' => env('API_URL_RPU', '') . '/get-office-ministry-list',
    'send_query_to_rpu' => env('API_URL_RPU', '') . '/send-audit-query',
    'send_memo_to_rpu' => env('API_URL_RPU', '') . '/send-audit-memo',
];
