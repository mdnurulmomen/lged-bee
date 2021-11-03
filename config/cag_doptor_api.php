<?php
return [
    'auth' => [
        'client_login_url' => env('API_URL_DOPTOR', '') . '/client/login',
        'client_id' => env('DOPTOR_CLIENT_ID', ''),
        'client_pass' => env('DOPTOR_CLIENT_PASS', ''),
    ],
    'widget' => env('API_URL_DOPTOR', '') . '/switch/widget',

    'offices' => env('API_URL_DOPTOR', '') . '/offices',
    'office_wise_designation' => env('API_URL_DOPTOR', '') . '/office/get-office-wise-designation',
    'office_and_grade_wise_designation' => env('API_URL_DOPTOR', '') . '/office/get-office-and-grade-wise-designation',
    'office_unit_designation_map' => env('API_URL_DOPTOR', '') . '/office/unit-designation-map',
    'office_unit_designation_employee_map' => env('API_URL_DOPTOR', '') . '/office/unit-designation-employee-map',
    'designation_role' => env('API_URL_DOPTOR', '') . '/office/designation-role',
    'office_employees' => env('API_URL_DOPTOR', '') . '/offices/employees',
    'employee_signatures' => env('API_URL_DOPTOR', '') . '/employee/signatures',
];
