<?php

return [
    /*
        |--------------------------------------------------------------------------
        | Token size
        |--------------------------------------------------------------------------
        |
        | Here you may specify the length of token to verify the identify.
        | Max value is 255 characters, it will be used if bigger value is set.
        |
        */
    'token_length' => 8,
    /*
        |--------------------------------------------------------------------------
        | Token lifetime
        |--------------------------------------------------------------------------
        |
        | Token lifetime in minutes
        |
        */
    'token_lifetime' => env('SMS_TOKEN_LIFETIME', 60),

    /*
        |--------------------------------------------------------------------------
        | Routes
        |--------------------------------------------------------------------------
        |
        |
        */
    'sms_auth_route' => 'sms_auth',

    'sms_login_route' => 'sms_login',

    'sms_verify_route' => 'sms_verify',

    'redirect_route' => '/',

    /*
        |--------------------------------------------------------------------------
        | Middleware to use
        |--------------------------------------------------------------------------
        |
        | Here you may specify the name of the middleware you'd like to use so that
        | the verify token and auth in system.
        |
        */
    'middleware' =>  [
        'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable
    |--------------------------------------------------------------------------
    |
    | Enable/Disable package routes
    |
    */
    'enable' => env('SMS_AUTH_ENABLE', false),

    /*
    |--------------------------------------------------------------------------
    | Username
    |--------------------------------------------------------------------------
    |
    | Username field to login
    |
    */
    'username' => env('SMS_AUTH_USERNAME', 'phone'),


    'sms_tokens_table' => env('SMS_TOKENS_TABLE', 'sms_tokens'),

    'user_model' => App\Models\User::class,

    'ubicual_token' => env('UBICUAL_TOKEN', null),

    'sender' => env('SMS_SENDER', 'REMITENTE'),

    'sms_text' => 'Código de acceso: %s',

];
