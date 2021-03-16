<?php

$router->group(['prefix' => 'distribution'], function () use ($router) {
	$router->group(['middleware' => ['auth:api']], function ($router) {
		$router->post('register', 'DistributionController@register')->name('distribution.register');

		$router->get('generalize', 'DistributionController@generalize')->name('distribution.agent.generalize');
		$router->get('share_login/{goods_no}', 'DistributionController@shareLogin')->name('distribution.goods.share.login');

		$router->get('cash/list', 'CashController@getCashList')->name('distribution.cash.list');
		$router->post('cash/apply', 'CashController@applyCash')->name('distribution.cash.apply');
		$router->get('cash/balanceSum', 'CashController@balanceSum')->name('distribution.cash.balanceSumbalanceSum');

		$router->get('statistics', 'DistributionDataController@statistics')->name('distribution.agent.center.statistics');
		$router->get('balance', 'DistributionDataController@balance')->name('distribution.agent.center.balance');
		$router->get('earnings', 'DistributionDataController@myEarnings')->name('distribution.myEarnings');
		$router->get('unearnings', 'DistributionDataController@myUnearnings')->name('distribution.myUnearnings');
		$router->get('cash/record', 'DistributionDataController@cashRecord')->name('distribution.cash.record');
		$router->get('agent/members', 'DistributionDataController@agentMembers')->name('distribution.agent.members');
		$router->get('order/details/{status}', 'DistributionDataController@orderDetails')->name('distribution.order.details');
		$router->get('miniProgram/order/details/{status}', 'DistributionDataController@orderDetailsMiniProgram')->name('distribution.miniProgram.order.details');
		$router->get('order/list', 'DistributionDataController@orderList')->name('distribution.order.list');
		$router->get('order/detail/{order_no}', 'DistributionDataController@detail')->name('distribution.order.detail');
		$router->get('status/count', 'DistributionDataController@statusCount')->name('distribution.agent.order.status.count');

		$router->get('Suit/base/{id}', 'SuitController@shareBase')->name('distribution.agent.Suit.base');
		$router->get('Suit/shareImage', 'SuitController@shareImage')->name('distribution.agent.Suit.shareImage');

		$router->post('createRelation', 'DistributionController@createRelation')->name('distribution.agent.createRelation');

		$router->get('getMiniShareHomeInfo', 'DistributionController@getMiniShareHomeInfo')->name('distribution.agent.getMiniShareHomeInfo');

		//二级分销
		$router->get('twitter/list', 'DistributionDataController@subAgentList')->name('distribution.agent.twitter.list');
	});

	$router->get('share/{goods_no}/{agent_code}', 'DistributionController@share')->name('distribution.goods.share');
	$router->get('goods/list', 'DistributionController@goodsList')->name('distribution.goods.list');
	$router->get('goods/detail/{goods_id}', 'DistributionController@goodsDetail')->name('distribution.goods.detail');
	$router->get('image/test', 'DistributionController@imageTest')->name('distribution.image.test');

	$router->get('createMiniShareImg', 'DistributionController@createMiniShareImg')->name('distribution.image.createMiniShareImg');
	$router->get('template', 'DistributionController@getTemplate')->name('distribution.image.getTemplate');
	$router->get('Suit/template', 'SuitController@getSuitTemplate')->name('distribution.image.getSuitTemplate');

	$router->get('getAgentInfo', 'DistributionController@getAgentInfo');
});