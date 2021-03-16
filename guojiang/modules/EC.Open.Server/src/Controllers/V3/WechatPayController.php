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
use EasyWeChat;
use GuoJiangClub\Component\Balance\BalanceRepository;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Repositories\OrderRepository;
use GuoJiangClub\Component\Payment\Models\Payment;
use GuoJiangClub\EC\Open\Core\Processor\OrderProcessor;
use iBrand\Component\Pay\Facades\Charge;
use Illuminate\Http\Request;
use DB;

class WechatPayController extends Controller
{
	private $orderRepository;
	private $balanceRepository;
	private $orderProcessor;

	public function __construct(OrderRepository $orderRepository, BalanceRepository $balanceRepository, OrderProcessor $orderProcessor)
	{
		$this->orderRepository   = $orderRepository;
		$this->balanceRepository = $balanceRepository;
		$this->orderProcessor    = $orderProcessor;
	}

	public function createCharge()
	{
		$user = request()->user();

		$order_no = request('order_no');

		if (!$order_no || !$order = $this->orderRepository->getOrderByNo($order_no)) {
			return $this->failed('订单不存在');
		}

		if ($user->cant('pay', $order)) {
			return $this->failed('无权操作此订单');
		}

		if ($order->status == Order::STATUS_INVALID) {
			return $this->failed('无法支付');
		}

		if ($order->getNeedPayAmount() == 0 && request('balance')) {
			return $this->success(['type' => 'balance']);
		}

		if ($order->getNeedPayAmount() === 0) {
			return $this->failed('无法支付，需支付金额为零');
		}

		$redirect_url = $this->getPayRedirectUrl();

		//余额已支付
		if ($balance = request('balance') AND is_numeric($balance) AND $balance > 0) {

			$balance = $balance * 100;

			$userBalance = $this->balanceRepository->getSum($user->id);

			if ($userBalance <= 0 OR $userBalance < $balance) {
				return $this->failed('余额不足');
			}

			//计算出余额能支付的金额，余额大于整个订单金额，则支付订单金额，如果小于，则支付余额
			$total = $order->getNeedPayAmount() <= $balance ? $order->getNeedPayAmount() : $balance;

			try {

				DB::beginTransaction();

				$payment = new Payment([
					'order_id' => $order->id,
					'channel'  => 'balance',
					//'order_type' => Order::class,
					'amount'   => $total,
					'status'   => Payment::STATUS_COMPLETED,
					'paid_at'  => Carbon::now(),
				]);

				$order->payments()->save($payment);

				$this->balanceRepository->addRecord(
					['user_id'     => $user->id,
					 'type'        => 'order_payment',
					 'note'        => '订单余额支付：' . $total / 100 . ',订单号：' . $order->order_no,
					 'value'       => -$total,
					 'origin_id'   => $payment->id,
					 'origin_type' => Payment::class,
					]);

				DB::commit();
			} catch (\Exception $exception) {

				DB::rollBack();

				\Log::info($exception->getMessage() . $exception->getTraceAsString());

				return $this->failed('余额支付失败');
			}

			$order = $this->orderRepository->getOrderByNo($order_no);

			//标明余额完成了所有的支付金额
			if ($order->getNeedPayAmount() == 0) {

				$this->orderProcessor->process($order);

				$name = 'balance';

				if (request('channel') == 'wx_lite') {
					return $this->success(compact('name'));
				} else {
					$redirect_url = settings('wechat_pay_success_url') . $order_no;

					return $this->success(compact('redirect_url'));
				}
			}
		}

		if (request('channel') == 'wx_pub_qr') {
			$charge = Charge::create(['channel' => 'wx_lite', 'order_no' => $order_no, 'amount' => $order->getNeedPayAmount(), 'client_ip' => \request()->getClientIp(), 'subject' => $order->getSubject(), 'body' => $order->getSubject(), 'extra' => ['openid' => \request('openid')]]);

			//这样做的目的是防止在发起支付的时候，同时订单也自动取消，导致用户付款了，但是订单状态是已取消状态
			$order = $this->orderRepository->getOrderByNo($order_no);
			if ($order->status == Order::STATUS_CANCEL) {
				return $this->failed('订单已失效，无法支付');
			}

			return $this->success(compact('charge'));
		}

		if (request('channel') == 'wx_lite') {
			$charge = Charge::create(['channel' => 'wx_lite', 'order_no' => $order_no, 'amount' => $order->getNeedPayAmount(), 'client_ip' => \request()->getClientIp(), 'subject' => $order->getSubject(), 'body' => $order->getSubject(), 'extra' => ['openid' => \request('openid')]]);

			//这样做的目的是防止在发起支付的时候，同时订单也自动取消，导致用户付款了，但是订单状态是已取消状态
			$order = $this->orderRepository->getOrderByNo($order_no);
			if ($order->status == Order::STATUS_CANCEL) {
				return $this->failed('订单已失效，无法支付');
			}

			return $this->success(compact('charge'));
		}

		//这样做的目的是防止在发起支付的时候，同时订单也自动取消，导致用户付款了，但是订单状态是已取消状态
		$order = $this->orderRepository->getOrderByNo($order_no);
		if ($order->status == Order::STATUS_CANCEL) {
			return $this->failed('订单已失效，无法支付');
		}

		return $this->success(compact('redirect_url'));
	}

	private function getPayRedirectUrl()
	{
		$type     = 'order';
		$order_no = request('order_no');
		$balance  = request('balance');

		$channel = request('channel');
		if ($channel == 'alipay_wap') {
			return route('ali.pay.charge', compact('channel', 'type', 'order_no', 'balance'));
		}

		if ($channel == 'wx_pub') {
			return route('wechat.pay.getCode', compact('channel', 'type', 'order_no', 'balance'));
		}

		return '';
	}
}
