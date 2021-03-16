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

use Carbon\Carbon;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\Component\Payment\Services\PaymentService;
use GuoJiangClub\Component\Point\Models\Point;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use iBrand\Component\Pay\Facades\Charge;
use iBrand\Component\Pay\Facades\PayNotify;

class PaymentController extends Controller
{
	private $payment;
	private $orderRepository;
	private $pointRepository;

	public function __construct(PaymentService $paymentService, OrderRepository $orderRepository, PointRepository $pointRepository)
	{
		$this->payment         = $paymentService;
		$this->orderRepository = $orderRepository;
		$this->pointRepository = $pointRepository;
	}

	public function paidSuccess()
	{
		$user     = request()->user();
		$order_no = request('order_no');

		if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
			return $this->failed('订单不存在');
		}

		if ($user->cant('update', $order)) {
			return $this->failed('无权操作.');
		}

		//在pay_debug=true 状态下，可以调用此接口直接更改订单支付状态
		if (config('ibrand.app.pay_debug') and request('charge_id')) {
			$charge                 = \iBrand\Component\Pay\Models\Charge::where('order_no', $order_no)->orderBy('created_at', 'desc')->first();
			$charge->transaction_no = '';
			$charge->time_paid      = Carbon::now();
			$charge->paid           = 1;
			$charge->channel        = 'test';
			$charge->amount         = $order->total;
			$charge->save();
			$order = PayNotify::success($charge->type, $charge);
		}

		if (!config('ibrand.app.pay_debug') and $order_no->total > 0) {
			//同步查询微信订单状态，防止异步通信失败导致订单状态更新失败

			$charge = Charge::find(request('charge_id'));

			$order = PayNotify::success($charge->type, $charge);
		}

		$multiGroupon = '';
		if (Order::TYPE_MULTI_GROUPON == $order->type) {
			$multiGroupon = MultiGrouponUsers::where('order_id', $order->id)->first();
		}

		$order->multiGroupon = $multiGroupon;
		if (Order::STATUS_PAY == $order->status) {
			$pointInfo = $this->getPointInfo($order);

			event('paid.success.template.message', [$order, request('form_id')]);

			return $this->success(['order' => $order, 'pointInfo' => $pointInfo, 'payment' => '微信支付']);
		}

		return $this->failed('支付失败');
	}

	private function getPointInfo($order)
	{
		$pointUsed  = Point::where(['item_type' => 'GuoJiangClub\Component\Order\Models\Order', 'item_id' => $order->id])->first();
		$pointUsed  = $pointUsed ? $pointUsed->value : 0;
		$pointAdded = 0;
		$items      = $order->getItems();
		foreach ($items as $item) {
			if ($item->units_total != 0) {
				$point = Point::where(['item_type' => 'GuoJiangClub\Component\Order\Models\OrderItem', 'item_id' => $item->id])->first();
				if ($point) {
					$pointAdded += $point->value;
				}
			}
		}

		$pointTotal = $this->pointRepository->getSumPointValid($order->user_id);

		return [
			'pointUsed'  => $pointUsed,
			'pointAdded' => $pointAdded,
			'pointTotal' => $pointTotal,
		];
	}
}
