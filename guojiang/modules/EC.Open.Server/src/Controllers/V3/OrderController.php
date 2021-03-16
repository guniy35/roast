<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\EC\Open\Server\Transformers\OrderTransformer;

class OrderController extends Controller
{
	protected $orderRepository;

	public function __construct(OrderRepository $orderRepository)
	{
		$this->orderRepository = $orderRepository;
	}

	public function getOrders()
	{
		//$orderConditions['channel'] = \request('channel') ? \request('channel') : Order::CHANNEL_EC;

		if (request('order_no')) {
			$orderConditions['order_no'] = request('order_no');
		}

		if (request('status')) {
			$orderConditions['status'] = request('status');
		} else {
			$orderConditions['status']  = ['status', '<>', 0];
			$orderConditions['status2'] = ['status', '<>', 9];
		}

		$orderConditions['user_id'] = request()->user()->id;

		$itemConditions = [];

		$limit = request('limit') ? request('limit') : 10;

		if ($criteria = request('criteria')) {
			$itemConditions['order_no']  = ['order_no', 'like', '%' . $criteria . '%'];
			$itemConditions['item_name'] = ['item_name', 'like', '%' . $criteria . '%'];
			$itemConditions['item_id']   = ['item_id', 'like', '%' . $criteria . '%'];

			$order = $this->orderRepository->getOrdersByCriteria($orderConditions, $itemConditions, $limit);
		} else {
			$order = $this->orderRepository->getOrdersByConditions($orderConditions, $itemConditions,
				$limit, ['items', 'shippings', 'adjustments', 'items.product', 'items.product.goods']);
		}

		$transformer = request('transformer') ? request('transformer') : 'list';

		return $this->response()->paginator($order, new OrderTransformer($transformer));
	}

	public function getOrderDetails($orderno)
	{
		$user = request()->user();

		$order = $this->orderRepository->getOrderByNo($orderno);

		if ($user->cant('update', $order)) {
			return $this->failed('无权操作');
		}

		return $this->response()->item($order, new OrderTransformer());
	}

	/**
	 * 获取能够进行售后维修的订单
	 */
	public function getRefundOrders()
	{
		$orderConditions = [];

		$orderConditions ['status']  = ['status', '<>', 0];
		$orderConditions ['status2'] = ['status', '<>', 9];
		$orderConditions ['status3'] = ['status', '<>', 1];
		$orderConditions ['status4'] = ['status', '<>', 8];
		$orderConditions ['status5'] = ['status', '<>', 6];
		$orderConditions ['status6'] = ['status', '<>', 5];

		$orderConditions ['user_id'] = request()->user()->id;
		$orderConditions ['type']    = ['type', '!=', Order::TYPE_POINT];

		$itemConditions = [];

		$limit = request('limit') ? request('limit') : 15;

		if ($criteria = request('criteria')) {
			$itemConditions['order_no']  = ['order_no', 'like', '%' . $criteria . '%'];
			$itemConditions['item_name'] = ['item_name', 'like', '%' . $criteria . '%'];
			$itemConditions['item_id']   = ['item_id', 'like', '%' . $criteria . '%'];
		}

		$order = $this->orderRepository->getOrdersByCriteria($orderConditions, $itemConditions, $limit);

		return $this->response()->paginator($order, new OrderTransformer('refund'));
	}

	public function getPointOrders()
	{
		$orderConditions = [];

		if (request('status')) {
			$orderConditions['status'] = request('status');
		} else {
			$orderConditions ['status']  = ['status', '<>', 0];
			$orderConditions ['status2'] = ['status', '<>', 9];
		}

		$orderConditions ['user_id'] = request()->user()->id;

		$itemConditions = [];

		$limit = request('limit') ? request('limit') : 15;

		$order = $this->orderRepository->getOrdersByConditions($orderConditions, $itemConditions, $limit, ['items']);

		return $this->response()->paginator($order, new OrderTransformer());
	}
}
