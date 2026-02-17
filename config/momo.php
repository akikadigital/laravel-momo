<?php

// config for Akika/MoMo
return [
    // Allowed Values: sandbox/production
    'env' => env('MOMO_ENV', 'sandbox'),

    'provider_callback_host' => env('MOMO_CALLBACK_HOST'),

    'production' => [
        'base_url' => env('MOMO_PRODUCTION_BASE_URL'),
        'secondary_key' => env('MOMO_PRODUCTION_SECONDARY_KEY'),

        // Format - UUID. Resource ID for the API user to be created. UUID version 4 is required.
        'user_reference_id' => env('MOMO_PRODUCTION_USER_REFERENCE_ID'),
    ],

    'sandbox' => [
        'base_url' => env('MOMO_SANDBOX_BASE_URL', 'https://sandbox.momodeveloper.mtn.com'),
        'secondary_key' => env('MOMO_SANDBOX_SECONDARY_KEY'),

        // Format - UUID. Resource ID for the API user. UUID version 4 is required.
        'user_reference_id' => env('MOMO_SANDBOX_USER_REFERENCE_ID'),
    ],

    'url_paths' => [
        'create_api_user' => env('MOMO_CREATE_API_USER_URL_PATH', '/v1_0/apiuser'),

        // referenceId - UUID. Resource ID for the API user. UUID version 4 is required.
        'get_api_user' => env('MOMO_GET_API_USER_URL_PATH', '/v1_0/apiuser/{referenceId}'),

        // referenceId - UUID. Resource ID for the API user. UUID version 4 is required.
        'create_api_key' => env('MOMO_CREATE_API_KEY_URL_PATH', '/v1_0/apiuser/{referenceId}/apikey'),
    ],

    /**
     * Automatically deposit funds to multiple users
     * https://momodeveloper.mtn.com/API-collections#api=disbursement
     */
    'disbursement' => [
        'url_paths' => [
            'create_access_token' => env('MOMO_DISBURSMENT_CREATE_ACCESS_TOKEN_URL_PATH', '/disbursement/token/'),
        ],
    ],
];
