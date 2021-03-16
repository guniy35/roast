<?php

namespace GuoJiangClub\Component\Balance\Listeners;

use GuoJiangClub\Component\Balance\Balance;
use GuoJiangClub\Component\Balance\BalanceRepository;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Refund\Models\Refund;
use GuoJiangClub\Component\Refund\Models\RefundAmount;

class BalanceRefundEventListener
{
	private $balanceRepository;

	public function __construct(BalanceRepository $balanceRepository)
	{
		$this->balanceRepository = $balanceRepository;
	}

	public function balanceRefund($refund)
	{
		$refundBalance = RefundAmount::where('refund_id', $refund->id)->where('type', 'balance')->first();
		if ($refundBalance) {
			$data = ['user_id'     => $refund->user_id,
			         'type'        => 'balance_refund',
			         'note'        => '订单退款返还余额：' . $refundBalance->amount / 100 . '元,售后订单号：' . $refund->refund_no,
			         'value'       => $refundBalance->amount,
			         'origin_id'   => $refund->id,
			         'origin_type' => Refund::class,
			];
			$this->balanceRepository->addRecord($data);
		}
	}

	public function cancelOrder($order_id)
	{
		$order = Order::find($order_id);
		if ($order->pay_status == 1) {
			return;
		}

		$balance_pay = $order->payments->where('channel', 'balance')->where('status', 'completed')->first();
		if (!$balance_pay) {
			return;
		}

		$data = ['user_id'     => $order->user_id,
		         'type'        => 'balance_refund',
		         'note'        => '订单取消余额返还：' . $balance_pay->amount / 100 . '元,订单号：' . $order->order_no,
		         'value'       => $balance_pay->amount,
		         'origin_id'   => $balance_pay->id,
		         'origin_type' => Balance::class,
		];

		$this->balanceRepository->addRecord($data);
	}

	public function subscribe($events)
	{
		$events->listen(
			'balance.refund',
			'GuoJiangClub\Component\Balance\Listeners\BalanceRefundEventListener@balanceRefund'
		);

		$events->listen(
			'order.canceled',
			'GuoJiangClub\Component\Balance\Listeners\BalanceRefundEventListener@cancelOrder'
		);
	}
}