<?php

/*
 * This file is part of ibrand/auth-api.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::post('oauth/sms', 'AuthController@smsLogin')->name('api.oauth.sms');
Route::post('oauth/app/sms', 'AuthController@appSmsLogin')->name('api.oauth.app.sms');

Route::get('oauth/official-account/get-redirect-url', 'OfficialAccountAuthController@getRedirectUrl');
Route::post('oauth/official-account/quick-login', 'OfficialAccountAuthController@quickLogin');
Route::post('oauth/official-account/quick-user-login', 'OfficialAccountAuthController@quickUserLogin');
Route::post('oauth/official-account/update-user', 'OfficialAccountAuthController@updateUser');

Route::post('oauth/miniprogram/login', 'MiniProgramLoginController@login')->name('api.oauth.miniprogram.login');
Route::post('oauth/miniprogram/user-login', 'MiniProgramLoginController@userLogin')->name('api.oauth.miniprogram.user.login');
Route::post('oauth/miniprogram/mobile', 'MiniProgramLoginController@mobileLogin')->name('api.oauth.miniprogram.mobile.login');
Route::get('oauth/miniprogram/openid', 'MiniProgramLoginController@getOpenIdByCode');
Route::post('oauth/app/unionid/login', 'MiniProgramLoginController@appUnionidLogin')->name('api.oauth.app.unionid.login');