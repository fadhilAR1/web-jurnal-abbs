<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | These paths are handled by the HandleCors middleware.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Methods
    |--------------------------------------------------------------------------
    |
    | This is the list of HTTP verbs that are allowed by CORS.
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Origins
    |--------------------------------------------------------------------------
    |
    | The origin(s) that are allowed to make requests.
    |
    */

    'allowed_origins' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Allowed Headers
    |--------------------------------------------------------------------------
    |
    | This is the list of HTTP headers that are allowed by CORS.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | CORS Exposed Headers
    |--------------------------------------------------------------------------
    |
    | This is the list of HTTP headers that are exposed to the client.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | CORS Max Age
    |--------------------------------------------------------------------------
    |
    | This is the max age of CORS requests.
    |
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | CORS Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Whether or not CORS requests can send credentials.
    |
    */

    'supports_credentials' => false,
];
