<?php

$router->group(['prefix' => 'store/discover', 'middleware' => ['discover.bootstrap']], function () use ($router) {
	$router->resource('category', 'CategoryController');
	$router->resource('tag', 'TagController');
	$router->resource('content', 'ContentController');
	$router->resource('banner', 'BannerController');
	$router->get('content/goods/list', 'ContentController@goodsList');
});
