<?php
return [
    'auth' => [
        'client_login_url' => env('API_URL_RPU', '') . '/client-login',
        'client_id' => env('RPU_CLIENT_ID', ''),
        'client_pass' => env('RPU_CLIENT_PASS', ''),
    ],
    'get-office-ministry-list' => env('API_URL_RPU', '') . '/get-office-ministry-list',
    'send_query_to_rpu' => env('API_URL_RPU', '') . '/send-audit-query',
    'update_query_to_rpu' => env('API_URL_RPU', '') . '/update-audit-query',
    'send_memo_to_rpu' => env('API_URL_RPU', '') . '/send-audit-memo',
    'update_memo_to_rpu' => env('API_URL_RPU', '') . '/update-audit-memo',
    'received_query_from_rpu' => env('API_URL_RPU', '') . '/receive-query-from-rpu',
    'remove_query_to_rpu' => env('API_URL_RPU', '') . '/remove-query-from-rpu',
];
