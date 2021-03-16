<?php

namespace GuoJiangClub\Distribution\Backend\Services;

use Carbon\Carbon;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Distribution\Backend\Models\AgentCommission;
use GuoJiangClub\Distribution\Backend\Repository\AgentRepository;
use GuoJiangClub\Distribution\Backend\Repository\OrdersRepository;

class DataStatisticsService
{
	protected $agentRepository;
	protected $agentOrderRepository;

	public function __construct(AgentRepository $agentRepository,
	                            OrdersRepository $ordersRepository)
	{
		$this->agentRepository      = $agentRepository;
		$this->agentOrderRepository = $ordersRepository;
	}

	/**
	 * 获取日报数据
	 *
	 * @param $time
	 *
	 * @return array
	 */
	public function getDayData($time)
	{
		/*日期*/
		$date = Carbon::yesterday()->format('Y-m-d');

		/*昨日分销员数*/
		$agentCount = $this->agentRepository->getAgentCountByDate($time);

		/*累计分销订单数*/
		$agentOrderCount = $this->agentOrderRepository->getAgentOrderData([], false);

		/*昨日分销订单数*/
		$agentOrderDayCount = $this->agentOrderRepository->getAgentOrderData($time, false);

		/*累计分销订单总金额*/
		$agentOrderAmount = $this->agentOrderRepository->getAgentOrderData([], true);

		/*昨日分销订单总金额*/
		$agentOrderDayAmount = $this->agentOrderRepository->getAgentOrderData($time, true);

		/*累计结算佣金*/
		$commission = $this->getCommissionCount([]);

		/*今日结算佣金*/
		$dayCommission = $this->getCommissionCount($time);

		/*待结算佣金*/
		$stateCommission = $this->agentOrderRepository->getAgentOrderCommissionData([]);

		return [
			$date,
			$agentCount,
			$agentOrderCount,
			$agentOrderDayCount,
			$agentOrderAmount,
			$agentOrderDayAmount,
			$commission,
			$dayCommission,
			$stateCommission,
		];
	}

	/**
	 * 获取上一月/前一天时间
	 *
	 * @param bool $month
	 *
	 * @return array
	 */
	public function getDate($month = false)
	{
		$start = Carbon::yesterday()->format('Y-m-d 00:00:00');
		$end   = Carbon::yesterday()->format('Y-m-d 23:59:59');

		if ($month) {
			$date  = date('Y-m', strtotime('-1 month'));
			$day   = date('t', strtotime('-1 month'));
			$start = $date . '-01 00:00:00';
			$end   = $date . '-' . $day . ' 23:59:59';
		}

		return [$start, $end];
	}

	/**
	 * 日/月结算金额
	 *
	 * @param $time
	 *
	 * @return float
	 */
	protected function getCommissionCount($time)
	{
		if (count($time) > 0) {
			return AgentCommission::whereBetween('created_at', $time)->sum('commission') / 100;
		}

		return AgentCommission::sum('commission') / 100;
	}

	/**
	 * 获取月报数据
	 *
	 * @param $time
	 *
	 * @return array
	 */
	public function getMonthData($time)
	{
		$date = date('Y-m', strtotime('-1 month'));

		/*本月平台总交易额*/
		$orderAmount = Order::whereBetween('created_at', $time)->where('pay_status', 1)->sum('total') / 100;

		/*平台注册分销员总数*/
		$agentCount = $this->agentRepository->all()->count();

		/*本月新增注册分销员数*/
		$agentMonthCount = $this->agentRepository->getAgentCountByDate($time);

		/*本月分销订单数*/
		$agentOrderMonthCount = $this->agentOrderRepository->getAgentOrderData($time, false);

		/*本月分销订单金额*/
		$agentOrderMonthAmount = $this->agentOrderRepository->getAgentOrderData($time, true);

		/*本月结算佣金*/
		$monthCommission = $this->getCommissionCount($time);

		/*待结算佣金*/
		$stateCommission = $this->agentOrderRepository->getAgentOrderCommissionData([]);

		return [
			$date,
			$orderAmount,
			$agentCount,
			$agentMonthCount,
			$agentOrderMonthCount,
			$agentOrderMonthAmount,
			$monthCommission,
			$stateCommission,
		];
	}
}