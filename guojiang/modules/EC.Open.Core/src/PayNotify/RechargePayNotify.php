<?php

namespace GuoJiangClub\EC\Open\Core\PayNotify;

use Carbon\Carbon;
use GuoJiangClub\Component\Balance\Balance;
use GuoJiangClub\Component\Recharge\Models\BalanceOrder;
use iBrand\Component\Pay\Contracts\PayNotifyContract;
use iBrand\Component\Pay\Models\Charge;

class RechargePayNotify implements PayNotifyContract
{
	public function success(Charge $charge)
	{
		$order = BalanceOrder::where('order_no', $charge->order_no)->first();
		if ($order AND $order->pay_status == 0 AND $order->pay_amount == $charge['amount']) {
			$order->pay_status = 1;
			$order->pay_time   = Carbon::now();
			$order->save();

			$sum = Balance::sumByUser($order->user_id);
			if (!is_numeric($sum)) {
				$sum = 0;
			} else {
				$sum = (int) $sum;
			}

			Balance::create(['user_id' => $order->user_id, 'type' => 'recharge', 'note' => '充值', 'value' => $order->amount, 'current_balance' => $sum + $order->amount, 'origin_id' => $order->id, 'origin_type' => BalanceOrder::class]);
		}
	}
}