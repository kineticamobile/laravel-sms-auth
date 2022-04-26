<?php

use Illuminate\Support\Facades\Route;
use Kineticamobile\SmsAuth\Middlewares\SmsAuthMiddleware;

Route::group(
    [
        'middleware' => config("sms-auth.middleware", ["web"]),
    ],
    function () {
        Route::get( config('sms-auth.sms_auth_route', 'sms_auth'),'Kineticamobile\SmsAuth\Controllers\SmsAuthController@sms_auth')->name('sms_auth');

        Route::post(config('sms-auth.sms_auth_route', 'sms_login'),'Kineticamobile\SmsAuth\Controllers\SmsAuthController@sms_login')->name('sms_login');

        Route::post(config('sms-auth.sms_auth_route', 'sms_verify'),'Kineticamobile\SmsAuth\Controllers\SmsAuthController@sms_verify')->name('sms_verify');
    }
);
