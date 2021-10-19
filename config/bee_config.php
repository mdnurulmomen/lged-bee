<?php

if (!defined("PER_PAGE_PAGINATION")) define("PER_PAGE_PAGINATION", 10);

return [
    'secret_key' => env('SECRET_KEY'),
    'is_keycloak_enabled' => true,
    'client_id' => 'bee-client',
    'client_pass' => '123456',
];
