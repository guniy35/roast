<?php

namespace GuoJiangClub\Distribution\Server\Http\Controllers;

use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Distribution\Core\Models\Agent;
use GuoJiangClub\Distribution\Core\Models\AgentOrder;
use GuoJiangClub\Distribution\Core\Models\BalanceCash;
use GuoJiangClub\Distribution\Server\Repository\AgentCashRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentCommissionRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentOrderItemRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentOrderRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentRelationRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentUserRelationRepository;
use GuoJiangClub\Distribution\Server\Services\AgentsService;
use GuoJiangClub\Distribution\Server\Transformers\AgentMembersTransformer;
use GuoJiangClub\Distribution\Server\Transformers\AgentTransformer;
use GuoJiangClub\Distribution\Server\Transformers\CashTransformer;
use GuoJiangClub\Distribution\Server\Transformers\CommissionTransformer;
use GuoJiangClub\Distribution\Server\Transformers\OrderDetailsTransformer;
use GuoJiangClub\Distribution\Server\Transformers\OrderListTransformer;

class DistributionDataController extends Controller
{
	protected $agent;
	protected $agentCommission;
	protected $agentRelation;
	protected $agentOrderItem;
	protected $agentCash;
	protected $agentUser;
	protected $agentOrder;
	protected $agentService;

	public function __construct(
		AgentRepository $agentRepository
		, AgentsService $agentsService
		, AgentOrderItemRepository $agentOrderItemRepository
		, AgentCashRepository $agentCashRepository
		, AgentCommissionRepository $agentCommissionRepository
		, AgentUserRelationRepository $agentUserRelationRepository
		, AgentOrderRepository $agentOrderRepository
		, AgentRelationRepository $agentRelationRepository)
	{
		$this->agent           = $agentRepository;
		$this->agentService    = $agentsService;
		$this->agentOrderItem  = $agentOrderItemRepository;
		$this->agentCash       = $agentCashRepository;
		$this->agentCommission = $agentCommissionRepository;
		$this->agentUser       = $agentUserRelationRepository;
		$this->agentOrder      = $agentOrderRepository;
		$this->agentRelation   = $agentRelationRepository;
	}

	/**
	 * 获取当前分销商id
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	protected function getAgent()
	{
		if (!isset(request()->user()->id)) {
			return null;
		}

		$user_id = request()->user()->id;
		if (!$user_id) {
			return $this->api([], false, 500, '您尚未登录');
		}

		$agent = $this->agent->with('user')->findWhere(['user_id' => $user_id])->first();
		if (!$agent) {
			return $this->api([], false, 500, '您尚未注册成为分销商');
		}

		if (!$agent->status) {
			return $this->api([], false, 500, '您的分销商注册申请尚未通过审核');
		}

		return $agent;
	}

	/**
	 * 分销中心 数据统计
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function statistics()
	{
		$agent                    = $this->getAgent();
		$inCase                   = [1, 2, 3, 4, 5];
		$today                    = date('Y-m-d', time());
		$today_start              = $today . ' 00:00:00';
		$today_end                = $today . ' 23:59:59';
		$day_seven_start          = date('Y-m-d 00:00:00', strtotime('-6 day'));
		$day_seven_end            = $today_end;
		$day_month_start          = date('Y-m-d 23:59:59', strtotime("-1 month +1 day", strtotime($today)));
		$day_month_end            = $today_end;
		$today_before_one_start   = date('Y-m-d 00:00:00', strtotime('-1 day'));
		$today_before_one_end     = date('Y-m-d 23:59:59', strtotime('-1 day'));
		$today_before_two_start   = date('Y-m-d 00:00:00', strtotime('-2 day'));
		$today_before_two_end     = date('Y-m-d 23:59:59', strtotime('-2 day'));
		$today_before_three_start = date('Y-m-d 00:00:00', strtotime('-3 day'));
		$today_before_three_end   = date('Y-m-d 23:59:59', strtotime('-3 day'));
		$today_before_fore_start  = date('Y-m-d 00:00:00', strtotime('-4 day'));
		$today_before_fore_end    = date('Y-m-d 23:59:59', strtotime('-4 day'));
		$today_before_five_start  = date('Y-m-d 00:00:00', strtotime('-5 day'));
		$today_before_five_end    = date('Y-m-d 23:59:59', strtotime('-5 day'));
		$today_before_six_start   = date('Y-m-d 00:00:00', strtotime('-6 day'));
		$today_before_six_end     = date('Y-m-d 23:59:59', strtotime('-6 day'));

		$user_count_today = $this->agentService->countUserByDate($agent->id, [$today_start, $today_end]);
		$user_count_seven = $this->agentService->countUserByDate($agent->id, [$day_seven_start, $day_seven_end]);
		$user_count_month = $this->agentService->countUserByDate($agent->id, [$day_month_start, $day_month_end]);

		$agent_order_count_today = $this->agentService->countAgentOrderByDate($agent->id, [$today_start, $today_end]);
		$agent_order_count_seven = $this->agentService->countAgentOrderByDate($agent->id, [$day_seven_start, $day_seven_end]);
		$agent_order_count_month = $this->agentService->countAgentOrderByDate($agent->id, [$day_month_start, $day_month_end]);

		$money_sum_today = $this->agentService->countAgentOrderTotalByDate($agent->id, [$today_start, $today_end], $inCase);
		$money_sum_seven = $this->agentService->countAgentOrderTotalByDate($agent->id, [$day_seven_start, $day_seven_end], $inCase);
		$money_sum_month = $this->agentService->countAgentOrderTotalByDate($agent->id, [$day_month_start, $day_month_end], $inCase);

		$money_sum__before_one   = $this->agentService->countAgentOrderTotalByDate($agent->id, [$today_before_one_start, $today_before_one_end], $inCase);
		$money_sum__before_two   = $this->agentService->countAgentOrderTotalByDate($agent->id, [$today_before_two_start, $today_before_two_end], $inCase);
		$money_sum__before_three = $this->agentService->countAgentOrderTotalByDate($agent->id, [$today_before_three_start, $today_before_three_end], $inCase);
		$money_sum__before_fore  = $this->agentService->countAgentOrderTotalByDate($agent->id, [$today_before_fore_start, $today_before_fore_end], $inCase);
		$money_sum__before_five  = $this->agentService->countAgentOrderTotalByDate($agent->id, [$today_before_five_start, $today_before_five_end], $inCase);
		$money_sum__before_six   = $this->agentService->countAgentOrderTotalByDate($agent->id, [$today_before_six_start, $today_before_six_end], $inCase);

		$return = [
			'user_count_today'        => $user_count_today,
			'user_count_seven'        => $user_count_seven,
			'user_count_month'        => $user_count_month,
			'agent_order_count_today' => $agent_order_count_today,
			'agent_order_count_seven' => $agent_order_count_seven,
			'agent_order_count_month' => $agent_order_count_month,
			'money_sum_today'         => $money_sum_today / 100,
			'money_sum_seven'         => $money_sum_seven / 100,
			'money_sum_month'         => $money_sum_month / 100,
			'graph'                   => [
				date('m-d', strtotime($today_before_six_start))   => $money_sum__before_six / 100,
				date('m-d', strtotime($today_before_five_start))  => $money_sum__before_five / 100,
				date('m-d', strtotime($today_before_fore_start))  => $money_sum__before_fore / 100,
				date('m-d', strtotime($today_before_three_start)) => $money_sum__before_three / 100,
				date('m-d', strtotime($today_before_two_start))   => $money_sum__before_two / 100,
				date('m-d', strtotime($today_before_one_start))   => $money_sum__before_one / 100,
				date('m-d', strtotime($today_start))              => $money_sum_today / 100,
			],
		];

		return $this->api($return, true, 200, '');
	}

	/**
	 * 累计收益 未到账收益 当前可提现余额
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function balance()
	{
		$agent   = $this->getAgent();
		$amount  = $this->agentCommission->getSumCommission($agent->id);
		$cash    = BalanceCash::where('agent_id', $agent->id)->where('status', '<>', 3)->sum('amount');
		$balance = $amount - $cash;

		$earnings_commission   = $this->agentCommission->getCommissionPaginated(['agent_id' => $agent->id], '', 0)->sum('commission');
		$unearnings_commission = $this->agentOrderItem->getOrderItemPaginated(['agent_id' => $agent->id, 'status' => 0], '', 0)->sum('commission');

		return $this->api(['balance'                 => number_format($balance / 100, 2, '.', '')
		                   , 'earnings_commission'   => number_format($earnings_commission / 100, 2, '.', '')
		                   , 'unearnings_commission' => number_format($unearnings_commission / 100, 2, '.', '')]
			, true, 200, '');
	}

	/**
	 * 我的收益-累计收益
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function myEarnings()
	{
		$agent = $this->getAgent();
		$list  = $this->agentCommission->getCommissionPaginated(['agent_id' => $agent->id], 'agentOrder');

		return $this->response()->paginator($list, new CommissionTransformer());
	}

	/**
	 * 我的收益-未到账收益
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function myUnearnings()
	{
		$agent = $this->getAgent();
		$list  = $this->agentOrderItem->getOrderItemPaginated(['agent_id' => $agent->id, 'status' => 0], 'agentOrder');

		return $this->response()->paginator($list, new CommissionTransformer());
	}

	/**
	 * 获取提交记录列表
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function cashRecord()
	{
		$agent = $this->getAgent();
		$list  = $this->agentCash->getAgentCashPaginated(['agent_id' => $agent->id]);

		return $this->response()->paginator($list, new CashTransformer());
	}

	public function subAgentList()
	{
		$agent = $this->getAgent();
		$limit = request('limit') ? request('limit') : 15;

		$where['status'] = Agent::STATUS_AUDITED;

		$agentUsers = $this->agentUser->findWhere(['agent_id' => $agent->id]);
		if ($agentUsers && $agentUsers->count() > 0) {
			$user_ids = $agentUsers->pluck('user_id')->all();
		} else {
			$user_ids = [0];
		}

		$relations = $this->agentRelation->findWhere(['parent_agent_id' => $agent->id]);
		if ($relations && $relations->count() > 0) {
			$agent_ids = $relations->pluck('agent_id')->all();
		} else {
			$agent_ids = [0];
		}

		$with = ['user'];
		$list = $this->agent->getAgentPaginated($where, $agent_ids, $user_ids, $with, $limit);

		return $this->response()->paginator($list, new AgentTransformer());
	}

	/**
	 * 会员列表
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function agentMembers()
	{
		$agent = $this->getAgent();
		$type  = request('type') ? request('type') : 'level1';

		if ($type == 'level1') {
			$list = $this->agentUser->getUserPaginated(['agent_id' => $agent->id], 'user');
		} else {
			$subAgents = $this->agentRelation->findWhere(['parent_agent_id' => $agent->id]);
			if ($subAgents && $subAgents->count() > 0) {
				$agent_ids = $subAgents->pluck('agent_id')->all();
			} else {
				$agent_ids = [0];
			}

			$list = $this->agentUser->getUserPaginated([], 'user', $agent_ids);
		}

		return $this->response()->paginator($list, new AgentMembersTransformer());
	}

	/**
	 * h5分销订单列表
	 *
	 * @param int $status 订单状态
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function orderDetails($status = 0)
	{
		$agent = $this->getAgent();
		$list  = $this->agentOrder->getOrderDetailsPaginated($agent->id, $status);

		return $this->response()->paginator($list, new OrderDetailsTransformer());
	}

	/**
	 * 小程序分销订单列表
	 *
	 * @param int $status 订单状态
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function orderDetailsMiniProgram($status = 0)
	{
		$agent = $this->getAgent();
		$type  = request('type') ? request('type') : 'all';

		$level1_agent_ids = [$agent->id];
		$agentUsers       = $this->agentUser->findWhere(['agent_id' => $agent->id]);
		if ($agentUsers && $agentUsers->count() > 0) {
			$user_ids = $agentUsers->pluck('user_id')->all();
		} else {
			$user_ids = [0];
		}

		$relations = $this->agentRelation->findWhere(['parent_agent_id' => $agent->id]);
		if ($relations && $relations->count() > 0) {
			$ids = $relations->pluck('agent_id')->all();
		} else {
			$ids = [0];
		}

		$list = $this->agent->getAgentPaginated(['status' => Agent::STATUS_AUDITED], $ids, $user_ids, [], 0);
		if ($list && $list->count() > 0) {
			$level2_agent_ids = $list->pluck('id')->all();
		} else {
			$level2_agent_ids = [0];
		}

		if ($type == 'level1') {
			$agent_ids = $level1_agent_ids;
		}

		if ($type == 'level2') {
			$agent_ids = $level2_agent_ids;
		}

		if ($type == 'all') {
			$agent_ids = array_unique(array_merge($level1_agent_ids, $level2_agent_ids));
		}

		$list = $this->agentOrder->getMiniProgramOrderDetailsPaginated($status, $agent_ids);

		return $this->response()->paginator($list, new OrderDetailsTransformer());
	}

	public function orderList()
	{
		$agent = $this->getAgent();
		$type  = request('type') ? request('type') : 'all';

		$where['agent_id'] = $agent->id;

		if ($type == 'level1') {
			$where['level'] = 1;;
		}

		if ($type == 'level2') {
			$where['level'] = 2;
		}

		if (request('status') == 'wait') {
			$where['status'] = 0;
		}

		if (request('status') == 'close') {
			$where['status'] = 1;
		}

		if (request('status') == 'lose') {
			$where['status'] = 2;
		}

		/*$level1_agent_ids = [$agent->id];
		$agentUsers       = $this->agentUser->findWhere(['agent_id' => $agent->id]);
		if ($agentUsers && $agentUsers->count() > 0) {
			$user_ids = $agentUsers->pluck('user_id')->all();
		} else {
			$user_ids = [0];
		}

		$relations = $this->agentRelation->findWhere(['parent_agent_id' => $agent->id]);
		if ($relations && $relations->count() > 0) {
			$ids = $relations->pluck('agent_id')->all();
		} else {
			$ids = [0];
		}

		$list = $this->agent->getAgentPaginated(['status' => Agent::STATUS_AUDITED], $ids, $user_ids, [], 0);
		if ($list && $list->count() > 0) {
			$level2_agent_ids = $list->pluck('id')->all();
		} else {
			$level2_agent_ids = [0];
		}

		if ($type == 'level1') {
			$agent_ids = $level1_agent_ids;
		}

		if ($type == 'level2') {
			$agent_ids = $level2_agent_ids;
		}

		if ($type == 'all') {
			$agent_ids = array_unique(array_merge($level1_agent_ids, $level2_agent_ids));
		}

		$where['agent_ids'] = $agent_ids;
		if (request('status') == 'wait') {
			$where['status'] = 0;
		}

		if (request('status') == 'close') {
			$where['status'] = 1;
		}

		if (request('status') == 'lose') {
			$where['status'] = 2;
		}*/

		$limit = request('limit') ? request('limit') : 10;
		$list  = $this->agentOrder->getAgentOrderPaginated($where, $limit);

		return $this->response()->paginator($list, new OrderListTransformer());
	}

	/**
	 * 分销订单详情
	 *
	 * @param $order_no
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function detail($order_no)
	{
		//临时修复问题：
		$user_order = Order::where('order_no', $order_no)->first();

		$agent = $this->getAgent();

		$order = $this->agentOrder->with('order')->with('order.items')
			->findWhere(['order_id' => $user_order->id, 'agent_id' => $agent->id])->first();

		if (!$order) {
			return $this->api([], false, 400, '订单不存在');
		}

		$has_refund = 0;
		$refunds    = $order->order->refunds()->where('status', '<>', 3)->get();
		if ($refunds && $refunds->count() > 0) {
			$has_refund = 1;
		}

		$order->order_discount = $order->order->adjustments()->where('origin_type', 'discount')->first();
		$order->order_counpon  = $order->order->adjustments()->where('origin_type', 'coupon')->first();

		$order->has_refund = $has_refund;

		//$order->agent_order_no = $order->order->order_no;

		$order->commission = number_format($order->commission / 100, 2, '.', '');

		return $this->api($order);
	}

	/**
	 * 获取每个状态下的订单数量
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	public function statusCount()
	{
		$agent  = $this->getAgent();
		$status = [
			Order::STATUS_NEW,
			Order::STATUS_PAY,
			Order::STATUS_DELIVERED,
			Order::STATUS_RECEIVED,
			Order::STATUS_COMPLETE,
		];

		$agentOrders = AgentOrder::where('agent_id', $agent->id)->join('ibrand_order', function ($join) use ($status) {
			$join->on('ibrand_agent_order.order_id', '=', 'ibrand_order.id')
				->whereIn('ibrand_order.status', $status);
		})->select('ibrand_order.*')->get();

		$status_new       = 0;
		$status_pay       = 0;
		$status_delivered = 0;
		$status_received  = 0;
		$status_complete  = 0;

		foreach ($agentOrders as $item) {
			switch ($item->status) {
				case Order::STATUS_NEW:
					$status_new++;
					break;
				case Order::STATUS_PAY:
					$status_pay++;
					break;
				case Order::STATUS_DELIVERED:
					$status_delivered++;
					break;
				case Order::STATUS_RECEIVED:
					$status_received++;
					break;
				case Order::STATUS_COMPLETE:
					$status_complete++;
					break;
			}
		}

		$return = [
			'status_new'       => $status_new,
			'status_pay'       => $status_pay,
			'status_delivered' => $status_delivered,
			'status_received'  => $status_received,
			'status_complete'  => $status_complete,
		];

		return $this->api($return, true, 200, '');
	}
}