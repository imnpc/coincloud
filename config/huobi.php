<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Configurations
    |--------------------------------------------------------------------------
    |
    | In this section you may define the default configuration for each model
    | that will be generated from any database.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | 账户信息
    |--------------------------------------------------------------------------
    |
    |
    */

    /*define('ACCOUNT_ID', ''); // 你的账户ID
    define('ACCESS_KEY',''); // 你的ACCESS_KEY
    define('SECRET_KEY', ''); // 你的SECRET_KEY*/
//    'api_url' => env('HUOBI_API_URL'),
//    'account_id' => env('HUOBI_ACCOUNT_ID'),
//    'access_key' => env('HUOBI_ACCESS_KEY'),
//    'secret_key' => env('HUOBI_SECRET_KEY'),
    'response_type' => 'array',
    'base_uri' => env('HUOBI_API_URL'),
    'app_key' => env('HUOBI_ACCESS_KEY'),
    'secret' => env('HUOBI_SECRET_KEY'),
];
