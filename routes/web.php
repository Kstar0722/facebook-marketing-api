<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;

Route::get('/', function () {
    return view('welcome');
});

Route::get( '/fb', function () {
//    $fbApi = new Api('curl');
    /*$account_id = 'act_'.env('FB_ADS_ACCOUNT_ID');
    try {
        $token = env('FB_ADS_APP_ID').'|'.env('FB_ADS_APP_SECRET');
        Api::init(env('FB_ADS_APP_ID'), env('FB_ADS_APP_SECRET'), $token);
        $account = new AdAccount($account_id);
        $cursor = $account->getCampaigns();

//        dd($account);
//        dd($account->getCampaigns());
    } catch (FacebookSDKException $e) {
        dd($e->getMessage());
    }*/
});


