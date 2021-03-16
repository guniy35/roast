<?php

/*
 * This file is part of ibrand/EC-Open-Core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Core\Processor;

use Carbon\Carbon;
use GuoJiangClub\Component\Order\Models\Order;
use Illuminate\Contracts\Events\Dispatcher;

class OrderProcessor
{
	protected $event;

	public function __construct(Dispatcher $event)
	{
		$this->event = $event;
	}

	public function create($order)
	{
		if ($order->count > 0) {
			$order->save();
			$this->event->fire('order.created');

			return $order;
		}

		return false;
	}

	public function process(Order $order)
	{
		if ($order->status == Order::STATUS_TEMP) {
			$this->submit($order);
		}

		if ($order->status == Order::STATUS_NEW) {
			$this->payOrder($order);
		}

		if ($order->status == Order::STATUS_DELIVERED) {
			$this->received($order);
		}

		if ($order->status == Order::STATUS_RECEIVED) {
			$this->review($order);
		}
	}

	/**
	 * cancel order.
	 *
	 * @param Order  $order
	 * @param string $cancelReason
	 *
	 * @return bool
	 */
	public function cancel(Order $order, $cancelReason = '用户取消')
	{
		if (Order::STATUS_NEW == $order->status) {
			$order->status          = Order::STATUS_CANCEL;
			$order->completion_time = Carbon::now();
			$order->cancel_reason   = $cancelReason;
			$order->save();
			event('order.canceled', $order->id);

			return true;
		}

		return false;
	}

	public function submit($order)
	{
		if (Order::STATUS_TEMP == $order->status) {
			$order->status      = Order::STATUS_NEW;
			$order->submit_time = Carbon::now();
			$order->save();
			event('order.submitted', [$order]);
		}
	}

	/**
	 * 支付订单
	 *
	 * @param $order
	 */
	private function payOrder(Order $order)
	{
		if ($order->total == 0) {
			$order->pay_type   = 'free';
			$order->pay_time   = Carbon::now();
			$order->status     = Order::STATUS_PAY;
			$order->pay_status = 1;
			$order->save();
			$this->event->fire('order.paid', [$order]);
		} else {
			if ($order->total <= $order->getPaidAmount()) {
				$order->status     = Order::STATUS_PAY;
				$order->pay_time   = Carbon::now();
				$order->pay_status = 1;
				$order->save();
				$this->event->fire('order.paid', [$order]);
			} else {
				$order->save();
			}
		}
	}

	public function received($order)
	{
		$order->status      = Order::STATUS_RECEIVED;
		$order->accept_time = Carbon::now();
		$order->save();
		$this->event->fire('order.received', [$order]);
	}

	private function review(Order $order)
	{
		if ($order->countItems() == $order->countComments()) {
			$order->status          = Order::STATUS_COMPLETE;
			$order->completion_time = Carbon::now();
			$order->save();
			$this->event->fire('order.reviewed', [$order]);
		}
	}
}
