<?php

/*
 * This file is part of ibrand/member-backend.
 *
 * (c) GuoJiangClub <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$router->group(['prefix' => 'admin/member'], function () use ($router) {

	$router->resource('users', 'UserController', ['except' => ['show'],
	                                              'names'  => [
		                                              'index'   => 'admin.users.index',
		                                              'create'  => 'admin.users.create',
		                                              'store'   => 'admin.users.store',
		                                              'edit'    => 'admin.users.edit',
		                                              'update'  => 'admin.users.update',
		                                              'destroy' => 'admin.users.destroy',
	                                              ],
	]);

	$router->get('users/banned', 'UserController@banned')->name('admin.users.banned');
	$router->post('users/getexport', 'UserController@getexport')->name('admin.users.getexport');
	$router->get('users/userexport', 'UserController@userexport')->name('admin.users.userexport');
	$router->get('users/download', 'UserController@download')->name('admin.users.download');

	$router->get('account/confirm/resend/{user_id}', 'UserController@resendConfirmationEmail')->name('admin.account.confirm.resend');

	$router->get('users/{id}/integrallist', 'UserController@integrallist')->name('admin.users.integrallist');
	$router->get('users/{id}/couponslist', 'UserController@couponslist')->name('admin.users.couponslist');
	$router->get('users/{id}/orderslist', 'UserController@orderslist')->name('admin.users.orderslist');

    $router->get('users/getUserPointData/{id}', 'UserController@getUserPointData')->name('admin.users.getUserPointList');

	$router->post('users/addPoint', 'UserController@addPoint')->name('admin.users.addPoint');

	$router->delete('users/{id}/everDelete', 'UserController@everDelete')->name('admin.users.everDelete');

	$router->get('users/getExportData', 'UserController@getExportData')->name('admin.users.getExportData');
	$router->get('users/edit/balance/list/{id}', 'BalanceController@getBalancePaginate')->name('admin.users.edit.balance.list');
	$router->post('users/edit/balance/add', 'BalanceController@operateBalance')->name('admin.users.edit.balance.operateBalance');

	$router->group(['prefix' => 'user/{id}', 'where' => ['id' => '[0-9]+']], function () use ($router) {
		$router->get('restore', 'UserController@restore')->name('admin.user.restore');
		$router->get('mark/{status}', 'UserController@mark')->name('admin.user.mark')->where(['status' => '[0,1,2]']);
		$router->get('password/change', 'UserController@changePassword')->name('admin.user.change-password');
		$router->post('password/change', 'UserController@updatePassword')->name('admin.user.change-password');
	});

	$router->get('users/importUser', 'UserController@importUser')->name('admin.users.importUser');
	$router->post('users/importUser/saveImport', 'UserController@saveImport')->name('admin.users.importUser.saveImport');
});

//会员积分
$router->group(['prefix' => 'admin/member/points'], function () use ($router) {
	$router->get('/', 'PointController@index')->name('admin.users.pointlist');

	$router->group(['prefix' => 'import'], function () use ($router) {
		$router->get('importPointModal', 'PointController@importPointModal')->name('admin.member.points.importPointModal');
		$router->get('getImportDataCount', 'PointController@getImportDataCount')->name('admin.member.points.getImportDataCount');
		$router->get('saveImportData', 'PointController@saveImportData')->name('admin.member.points.saveImportData');
	});
});

//会员余额
$router->group(['prefix' => 'admin/member/balances'], function () use ($router) {
	$router->get('/', 'BalanceController@index')->name('admin.users.balances.list');
	$router->get('importBalance/modal', 'BalanceController@importBalance')->name('admin.users.balance.importBalance');
	$router->post('importBalance/saveBalanceImport', 'BalanceController@saveBalanceImport')->name('admin.users.balance.saveBalanceImport');
});

//储值管理
$router->group(['prefix' => 'admin/member/recharge'], function () use ($router) {
	$router->get('/', 'RechargeController@index')->name('admin.users.recharge.index');

	$router->get('/create', 'RechargeController@create')->name('admin.users.recharge.create');

	$router->post('/store', 'RechargeController@store')->name('admin.users.recharge.store');

	$router->get('/{id}/edit', 'RechargeController@edit')->name('admin.users.recharge.edit');

	$router->post('/{id}/update', 'RechargeController@update')->name('admin.users.recharge.update');

	$router->post('/{id}/delete', 'RechargeController@destroy')->name('admin.users.recharge.delete');

	$router->post('/toggleStatus', 'RechargeController@toggleStatus')->name('admin.users.recharge.toggleStatus');

	$router->get('/api/coupon', 'RechargeController@coupon_api')->name('admin.users.recharge.api.coupon');
});

$router->group(['prefix' => 'admin/member/log_recharge'], function () use ($router) {
	$router->get('/', 'RechargeController@log')->name('admin.users.recharge.log.index');
});