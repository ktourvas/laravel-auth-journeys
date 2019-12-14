<?php

Route::group([ 'middleware' => [ 'web' ] ], function () {

    Route::get( 'login', 'laravel\auth\journeys\Http\Controllers\Auth\LoginController@showLoginForm');

    Route::post('login', 'laravel\auth\journeys\Http\Controllers\Auth\LoginController@login')
        ->name('login');

    Route::get('register', 'laravel\auth\journeys\Http\Controllers\Auth\RegisterController@showRegistrationForm');

    Route::post('register', 'laravel\auth\journeys\Http\Controllers\Auth\RegisterController@register')
        ->name('register');

    Route::group([ 'middleware' => [
        'auth',
//        'inactiveLogout'
    ] ], function () {

        Route::post('/logout', 'laravel\auth\journeys\Http\Controllers\Auth\LoginController@logout')->name('logout');

        Route::get('/password/change', 'laravel\auth\journeys\Http\Controllers\Auth\PasswordController@showChangePassword')->name('password.change.form');

        Route::post('/password/change', 'laravel\auth\journeys\Http\Controllers\Auth\PasswordController@changePassword')->name('password.change');

    });

    Route::get('password/reset', 'laravel\auth\journeys\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')
        ->name('password.request');

    Route::post('password/email', 'laravel\auth\journeys\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')
        ->name('password.email');

    Route::get('password/reset/{token}', 'laravel\auth\journeys\Http\Controllers\Auth\ResetPasswordController@showResetForm')
        ->name('password.reset');

    Route::post('password/reset', 'laravel\auth\journeys\Http\Controllers\Auth\ResetPasswordController@reset')
        ->name('password.update');


});