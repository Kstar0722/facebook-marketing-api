<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return auth()->user();
});

Route::group(['middleware' => ['jwt-auth', 'cors']], function () {
    Route::group(['namespace' => 'Dashboard'], function () {
        Route::get('keywords', 'FacebookAdsController@getAllKeywordsStats');
        Route::post('insights', 'FacebookInsightsController@insights');
        Route::resource('user', 'ApiUserController')->only(['show', 'update', 'destroy']);
        Route::get('product/list', 'ProductController@index');
        Route::resource('product', 'ProductController')->only(['show', 'store', 'update', 'destroy']);
        Route::get('product/{id}/ad_account/list', 'ProductController@getAdAccounts');
        Route::get('ad_account/list', 'AdAccountController@accounts');
        Route::put('ad_account/{id}', 'AdAccountController@update');
    });
});

Route::group(['namespace' => 'Dashboard', 'middleware' => ['cors']], function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', 'ApiAuthController@login');
        Route::post('register', 'ApiAuthController@register');
    });
});

Route::group(['namespace' => 'Auth', 'middleware' => ['cors']], function () {
    Route::prefix('password')->group(function () {
        Route::post('email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::post('create', 'ResetPasswordController@create');
        Route::post('reset', 'ResetPasswordController@reset')->name('password.update');
    });
});