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
    'send_air_to_rpu' => env('API_URL_RPU', '') . '/send-air-to-rpu',
    'broad_sheet_apotti_update' => env('API_URL_RPU', '') . '/broad-sheet-apotti-update',
    'broad_sheet_reply_from_directorate' => env('API_URL_RPU', '') . '/broad-sheet-reply-from-directorate',
    'apotti_final_status_update_to_rpu' => env('API_URL_RPU', '') . '/apotti-final-status-update-to-rpu',
    'send_meeting_apotti_to_rpu' => env('API_URL_RPU', '') . '/send-meeting-apotti-to-rpu',
    'get-offices-info' => env('API_URL_RPU', '') . '/get-offices-info',
    'archive-migrate-apotti-to-rpu' => env('API_URL_RPU', '') . '/archive/migrate-apotti-to-rpu',
    'archive-migrate-report-to-rpu' => env('API_URL_RPU', '') . '/archive/migrate-report-to-rpu',
    'store-edited-apotti' => env('API_URL_RPU', '') . '/apotti/store-edited-apotti',
];
