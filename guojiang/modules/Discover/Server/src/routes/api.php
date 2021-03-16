<?php

$router->group(['prefix' => 'discover'], function ($router) {
	$router->get('banners', 'DiscoverController@banners');
	$router->get('conditions', 'DiscoverController@conditions');
	$router->get('content/list', 'DiscoverController@list');
});

$router->group(['middleware' => ['auth:api']], function ($router) {

	$router->group(['prefix' => 'content/favorite'], function ($router) {
		$router->post('add', 'FavoriteController@addFavorite');
		$router->post('delete', 'FavoriteController@deleteFavorite');
		$router->get('list', 'FavoriteController@index');
	});
});

$router->group(['prefix' => 'brand'], function ($router) {
	$router->get('detail/{id}', 'BrandController@detail');
	$router->get('goods/list/{id}', 'BrandController@list');
});

$router->get('free/goods/list', 'GoodsController@list');
$router->get('free/goods/share/image', 'GoodsController@createFreeGoodsShareImage');
$router->get('free/goods/share/image/template', 'GoodsController@freeGoodsShareImageTemplate');
$router->get('vip/goods/list', 'GoodsController@vipGoodsList');
$router->get('member/privilege/list', 'PrivilegeController@list');
$router->get('user/agreement', 'PrivilegeController@agreement');
$router->get('user/center/img_list', 'PrivilegeController@imgList');