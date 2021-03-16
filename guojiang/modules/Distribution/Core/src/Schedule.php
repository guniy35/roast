<?php

namespace GuoJiangClub\Distribution\Core;

use GuoJiangClub\Distribution\Core\Models\AgentOrder;
use GuoJiangClub\Scheduling\Scheduling;
use GuoJiangClub\Component\Refund\Models\Refund;

class Schedule extends Scheduling
{

	public function schedule()
	{
		//佣金在订单收货后XX天生效
		$this->schedule->call(function () {
			if (env('MAODA_COMMISSION')) {
				return;
			}
			\Log::info('进入分销订单佣金结算定时任务');

			$delay      = app('system_setting')->getSetting('order_can_refund_day') ? app('system_setting')->getSetting('order_can_refund_day') : 7;
			$orderTable = 'ibrand_order';
			$agentOrder = 'ibrand_agent_order';

			$agentOrders = AgentOrder::join($orderTable, $orderTable . '.id', '=', $agentOrder . '.order_id')
				->where($agentOrder . '.status', 0)
				->where($agentOrder . '.level', 1)
				->whereNotNull($orderTable . '.accept_time')
				->whereRaw('(DATEDIFF(now(),ibrand_order.accept_time) >= ' . $delay . ')')
				->get();

			if (count($agentOrders) > 0) {
				foreach ($agentOrders as $item) {
					$status = [2, 3, 4];
					$refund = Refund::where('order_id', $item->order_id)
						->whereNotIn('status', $status)->first();
					if (!$refund) {
						event('agent.commission.state', $item->order);
					}
				}
			}
		})->daily();
	}

}