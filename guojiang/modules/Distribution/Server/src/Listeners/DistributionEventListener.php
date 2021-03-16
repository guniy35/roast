<?php

namespace GuoJiangClub\Distribution\Server\Listeners;

use Carbon\Carbon;
use GuoJiangClub\Component\Order\Models\Order;
use GuoJiangClub\Component\Order\Models\SpecialType;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Distribution\Backend\Models\Agent;
use GuoJiangClub\Distribution\Core\Models\AgentGoods;
use GuoJiangClub\Distribution\Server\Repository\AgentOrderRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentOrderItemRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentUserRelationRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentRelationRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentRepository;
use GuoJiangClub\Distribution\Server\Repository\OrderItemRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentGoodsRepository;
use GuoJiangClub\Distribution\Server\Repository\AgentCommissionRepository;
use GuoJiangClub\Distribution\Server\Repository\RefundRepository;
use GuoJiangClub\Distribution\Server\Repository\OrderRepository;
use GuoJiangClub\Component\Balance\BalanceRepository;
use GuoJiangClub\Component\MultiGroupon\Models\MultiGrouponUsers;
use GuoJiangClub\Component\Order\Models\Adjustment;
use GuoJiangClub\Component\Discount\Repositories\CouponRepository;
use Log;
use DB;

class DistributionEventListener
{
	protected $agent;
	protected $agentOrder;
	protected $agentOrderItem;
	protected $agentRelation;
	protected $agentUserRelation;
	protected $orderItem;
	protected $agentGoods;
	protected $agentCommission;
	protected $order;
	protected $balance;
	protected $refund;
	protected $couponRepository;

	public function __construct(
		AgentOrderRepository $agentOrderRepository,
		AgentOrderItemRepository $agentOrderItemRepository,
		AgentRepository $agentRepository,
		AgentUserRelationRepository $agentUserRelationRepository,
		AgentRelationRepository $agentRelationRepository,
		OrderItemRepository $orderItemRepository,
		AgentGoodsRepository $agentGoodsRepository,
		AgentCommissionRepository $agentCommissionRepository,
		OrderRepository $orderRepository,
		BalanceRepository $balanceRepository,
		RefundRepository $refundRepository,
		CouponRepository $couponRepository
	)
	{
		$this->agent             = $agentRepository;
		$this->agentOrder        = $agentOrderRepository;
		$this->agentOrderItem    = $agentOrderItemRepository;
		$this->agentRelation     = $agentRelationRepository;
		$this->agentUserRelation = $agentUserRelationRepository;
		$this->orderItem         = $orderItemRepository;
		$this->agentGoods        = $agentGoodsRepository;
		$this->agentCommission   = $agentCommissionRepository;
		$this->order             = $orderRepository;
		$this->balance           = $balanceRepository;
		$this->refund            = $refundRepository;
		$this->couponRepository  = $couponRepository;
	}

	public function userIdentify(Order $order, $current_agent_code, $current_agent_goodsIds)
	{
		$user_id       = $order->user_id;
		$data          = [];
		$userRelation  = $this->agentUserRelation->findWhere(['user_id' => $user_id])->first();
		$current_agent = '';

		if (!$userRelation AND !$current_agent_code) {
			if ($agent = $this->agent->findWhere(['user_id' => $order->user_id, 'status' => 1])->first() AND
				settings('distribution_self_commission')
			) {
				return $this->confirmArray($agent);
			}

			return $data;
		}

		if ($current_agent_code) {
			$current_agent = $this->agent->findWhere(['code' => $current_agent_code, 'status' => 1])->first();
		}

		if ($userRelation AND $current_agent) {
			$agent = $this->agent->findWhere(['id' => $userRelation->agent_id, 'status' => 1])->first();

			if ($agent AND $current_agent_code != $agent->code) {
				if (count($current_agent_goodsIds) < 1) {
					if ($user_id == $current_agent->user_id) {
						if (settings('distribution_self_commission')) {
							return $this->confirmArray($current_agent);
						} else {
							return $this->confirmArray($agent);
						}
					} else {
						if (settings('distribution_commission_for_link')) {   //如果设置按照分享链接计算佣金
							return $this->confirmArray($current_agent);
						} else {
							return $this->confirmArray($agent);
						}
					}
				} else {
					if (!$this->confirmAgentGoods($order, $current_agent_goodsIds)) {
						\Log::info('其他分销员分享的商品 不再订单商品中');

						return $this->confirmArray($agent);
					} else {
						if ($user_id == $current_agent->user_id) {
							if (settings('distribution_self_commission')) {
								\Log::info('双条件下自己购买获得佣金1' . $order->order_no);

								return $this->confirmArray($current_agent);
							} else {
								\Log::info('双条件下自己购买没有佣金1' . $order->order_no);

								return $this->confirmArray($agent);
							}
						} else {
							if (settings('distribution_commission_for_link')) {   //如果设置按照分享链接计算佣金
								\Log::info('设置按照分享链接计算佣金1');

								return $this->confirmArray($current_agent);
							} else {
								return $this->confirmArray($agent);
							}
						}
					}
				}
			}

			if ($agent AND $current_agent_code == $agent->code) {
				return $this->confirmArray($agent);
			}

			if (!$agent) {
				if (!$this->confirmAgentGoods($order, $current_agent_goodsIds)) {
					\Log::info('没有agent');

					return $data;
				} else {
					if ($user_id == $current_agent->user_id) {
						if (settings('distribution_self_commission')) {
							\Log::info('双条件下自己购买可获得佣金2' . $order->order_no);

							return $this->confirmArray($current_agent);
						}
						\Log::info('双条件下自己购买没有佣金2' . $order->order_no);
					} else {
						if (settings('distribution_commission_for_link')) {   //如果设置按照分享链接计算佣金
							\Log::info('设置按照分享链接计算佣金2');

							return $this->confirmArray($current_agent);
						}
					}
				}
			}
			/*if (!$agent) {
				if (!$this->confirmAgentGoods($order, $current_agent_goodsIds)) {
					\Log::info('没有agent');
					return $data;
				} else {
					if ($user_id == $current_agent->user_id) {
						if (settings('distribution_self_commission')) {
							\Log::info('双条件下自己购买可获得佣金2' . $order->order_no);
							return $this->confirmArray($current_agent);
						}
						\Log::info('双条件下自己购买没有佣金2' . $order->order_no);
					} else {
						return $this->confirmArray($current_agent);
					}
				}
			}*/
		} elseif (!$userRelation AND $current_agent) {
			if (!$this->confirmAgentGoods($order, $current_agent_goodsIds)) {
				\Log::info('分销员分享的商品 不再订单商品中');

				return $data;
			} elseif ($user_id == $current_agent->user_id) {
				if (settings('distribution_self_commission')) {
					\Log::info('current条件下自己购买可获得佣金' . $order->order_no);

					return $this->confirmArray($current_agent);
				}
				\Log::info('current条件下自己购买没有佣金' . $order->order_no);
			} else {//ok
				if (settings('distribution_commission_for_link')) {   //如果设置按照分享链接计算佣金
					\Log::info('设置按照分享链接计算佣金3');

					return $this->confirmArray($current_agent);
				}
			}
		} elseif ($userRelation AND !$current_agent) {
			if (!$agent = $this->agent->find($userRelation->agent_id) OR $agent->status != 1) {
				return $data;
			}

			return $this->confirmArray($agent);
		}

		return $data;
	}

	private function confirmArray($agentData)
	{
		$agentData     = $agentData->toArray();
		$data          = ['parent_agent_id' => $agentData['id'], 'level' => 1];
		$agentRelation = $this->agentRelation->findWhere(['agent_id' => $agentData['id']]);

		if (count($agentRelation) > 0) {
			$agentRelation = $agentRelation->toArray();
			array_unshift($agentRelation, $data);
			$data = ['agent' => $agentData, 'agentRelation' => $agentRelation];
		} else {
			$data = ['agent' => ['id' => 0], 'agentRelation' => [$data]];
		}

		$data['agent_code'] = $agentData['code'];

		return $data;
	}

	/**
	 * 判断分享商品的缓存时间
	 *
	 * @param $agent_goodsIds
	 * @param $is_goods :是否是商品 true false(套餐)
	 *
	 * @return array
	 */
	private function confirmGoodsTime($agent_goodsIds, $is_goods = true)
	{
		$goods_ids = [];

		if ($is_goods) {
			foreach ($agent_goodsIds as $value) {
				$agent_goods = AgentGoods::where('goods_id', $value['id'])->first();
				if ((int) ($value['time'] / 1000) > time() AND $agent_goods AND $agent_goods->activity == 1) {
					$goods_ids[] = $value['id'];
				}
			}

			return $goods_ids;
		} else {    //如果是套餐
			$suit = $agent_goodsIds[0];
			if (!$specialType = SpecialType::where('origin_type', 'Suit')->where('origin_id', $suit['id'])->first()) {
				return false;
			}

			if ((int) ($suit['time'] / 1000) > time()) {
				return true;
			}

			return false;
		}
	}

	/**
	 * 判断订单中的商品是否有访问过的分销商品
	 *
	 * @param Order $order
	 * @param       $agent_goodsIds
	 *
	 * @return bool
	 */
	private function confirmAgentGoods(Order $order, $agent_goodsIds)
	{
		if (count($agent_goodsIds) < 1) {
			return false;
		}

		if ($order->type == Order::TYPE_SUIT) { //如果是套餐订单
			if (!$specialType = SpecialType::where('origin_type', 'Suit')->where('order_id', $order->id)->first()) {
				return false;
			}
			$suit = array_filter($agent_goodsIds, function ($t) use ($specialType) {
				return $t['id'] == $specialType->origin_id;
			});
			$suit = array_values($suit); //重组下标
			if (count($suit) > 0 AND $this->confirmGoodsTime($suit, false)) {
				return true;
			}
		} else {
			$order_goods = [];
			foreach ($order->items as $item) {
				$order_goods[] = $item->item_meta['detail_id'];
			}

			$goods_ids = $this->confirmGoodsTime($agent_goodsIds);

			$intersection = array_intersect($order_goods, $goods_ids);
			if (count($intersection) > 0) {
				return true;
			}
		}

		return false;
	}

	/**
	 * 订单提交 佣金信息记录
	 *
	 * @param \GuoJiangClub\Distribution\Core\Models\Agent $agent
	 */
	public function agentOrderSubmitted(Order $order)
	{
		if (!settings('distribution_status') OR env('MAODA_COMMISSION')) {
			return true;
		}

		if ($order->type == Order::TYPE_SUIT) {

			$agent_goods_id = request('agent_suit_id');
			\Log::info('agent_suit_id' . json_encode($agent_goods_id));
		} else { //如果不是套餐订单,去校验订单里面的商品是否开启分销
			$filtered = $order->items->filter(function ($value, $key) {
				$agent_goods = AgentGoods::where('goods_id', $value->item_meta['detail_id'])->first();

				return $agent_goods AND $agent_goods->activity == 1;
			});
			if (count($filtered) < 1) {
				Log::info('订单里面的商品没有开启分销: ' . $order->order_no);

				return true;
			}
			$agent_goods_id = request('agent_goods_id');
		}

		if ($order->type == Order::TYPE_MULTI_GROUPON) { //如果是拼团订单，佣金结算给团长

			$relations = $this->checkGrouponOrder($order->id);
		}/* elseif ($order->type == Order::TYPE_ACTIVITY_TEMP) {  //如果是活动订单，佣金结算给教练

			$relations = $this->checkActivityOrder($order->id);
		} */ elseif ($agent_relations = $this->checkAgentCouponOrder($order->id)) {//如果使用推客分享优惠券，佣金结算给推客

			$relations = $agent_relations;
		} else {
			$relations = $this->userIdentify($order, request('agent_code'), is_array($agent_goods_id) ? $agent_goods_id : []);
		}

		if (empty($relations)) {
			Log::info('该订单不是分销订单, 订单号: ' . $order->order_no);

			return true;
		}

		$orderItem = $this->orderItem->findWhere(['order_id' => $order->id]);
		if (count($orderItem) <= 0) {
			Log::info('该订单不是分销订单no_items, 订单号: ' . $order->order_no);

			return true;
		}

		/*$agent_code = request('agent_code') ? request('agent_code') : $relations['agent_code'];*/
		$agent_code    = $relations['agent_code'];
		$sourceAgent   = $this->agent->findWhere(['code' => $agent_code])->first();
		$sourceAgentId = 0;
		if ($sourceAgent) {
			$sourceAgentId = $sourceAgent->id;
		}

		$rate           = settings('distribution_rate');
		$sysAgentLevel  = settings('distribution_level');
		$tmp_commission = [];
		DB::beginTransaction();
		foreach ($relations['agentRelation'] as $relation) {
			$level = $relation['level'];
			if ($level > $sysAgentLevel) {
				Log::info('层级超过系统设定');
				continue;
			}

			$agentCheck = $this->agent->find($relation['parent_agent_id']);
			if ($agentCheck->status != 1) {
				Log::info('分销商不合法 分销商id: ' . $relation['parent_agent_id']);
				continue;
			}

			$agentOrder = [
				'agent_id'       => $relation['parent_agent_id'],
				'from_agent_id'  => $sourceAgentId,
				'level'          => $relation['level'],
				'agent_order_no' => build_order_no('D'),
				'order_id'       => $order->id,
			];
			$res        = $this->agentOrder->create($agentOrder);

			$tmp_rate   = array_filter($rate, function ($item) use ($level) {
				return $item['key'] == $level;
			});
			$tmp_rate   = array_values($tmp_rate);
			$goods_rate = 0;  //商品佣金比例
			if ($order->type == Order::TYPE_SUIT) {
				if ($specialType = $order->specialTypes->first()) {
					$goods_rate = $specialType->suit->rate;
				}
			}

			if ($order->type == Order::TYPE_MULTI_GROUPON) { //多人拼团
				if ($specialType = $order->specialTypes->first()) {
					$goods_rate = $specialType->multiGroupon->rate;
				}
			}

			foreach ($orderItem as $item) {
				if ($order->type != Order::TYPE_SUIT AND $order->type != Order::TYPE_MULTI_GROUPON) {
					$itemDetail   = $item->item_meta;
					$isAgentGoods = $this->agentGoods->findWhere(['goods_id' => $itemDetail['detail_id']])->first();
					if (!$isAgentGoods || ($isAgentGoods && 0 == $isAgentGoods->activity)) {
						continue;
					}

					$goods_rate = $isAgentGoods->getRoleRate($sourceAgent->type);

					/*秒杀佣金*/
					/*if ($order->type == Order::TYPE_SECKILL
						AND $specialTypes = $order->specialTypes->first()
						AND $seckillItem = SeckillItem::find($specialTypes->origin_id)
					) {
						$goods_rate = $seckillItem->rate;
					}*/

					/*拼团佣金*/
					/*if ($order->type == Order::TYPE_GROUPON
						AND $specialTypes = $order->specialTypes->first()
						AND $grouponItem = GrouponItem::find($specialTypes->origin_id)
					) {
						$goods_rate = $grouponItem->rate;
					}*/
				}

				$total_commission = ($goods_rate / 100) * $item->total;
				$commission       = ($goods_rate / 100) * $item->total * ($tmp_rate[0]['value'] / 100);
				$agentOrderItem   = [
					'agent_id'         => $relation['parent_agent_id'],
					'order_item_id'    => $item->id,
					'agent_order_id'   => $res->id,
					'rate'             => $goods_rate,
					'total_commission' => intval($total_commission),
					'commission'       => intval($commission),
				];

				$this->agentOrderItem->create($agentOrderItem);
				$tmp_commission[$res->id][] = ['total_commission' => intval($total_commission), 'commission' => intval($commission)];
			}
		}

		DB::commit();

		foreach ($tmp_commission as $key => $value) {
			$sum_total_commission = array_sum(array_column($value, 'total_commission'));
			$sum_commission       = array_sum(array_column($value, 'commission'));
			$attributes           = [
				'total_commission' => $sum_total_commission,
				'commission'       => $sum_commission,
			];
			$this->agentOrder->update($attributes, $key);
		}

		return true;
	}

	/**
	 * 推客优惠券订单
	 */
	protected function checkAgentCouponOrder($order_id)
	{

		if ($adjustment = Adjustment::where('order_id', $order_id)->where('origin_type', 'coupon')->first()) {

			if ($coupon = $this->couponRepository->findWhere(['id' => $adjustment->origin_id])->first()) {

				if ($coupon AND
					$agent = $this->agent->findWhere(['id' => $coupon->utm_source, 'status' => 1])->first()
				) {
					return $this->confirmArray($agent);
				}
			}
		};

		return null;
	}

	/**
	 * 小拼团订单
	 */
	protected function checkGrouponOrder($order_id)
	{
		if ($grouponUser = MultiGrouponUsers::where('order_id', $order_id)->first()) {
			$leader = MultiGrouponUsers::where('multi_groupon_items_id', $grouponUser->multi_groupon_items_id)
				->where('is_leader', 1)->first();
			if ($leader AND
				$agent = $this->agent->findWhere(['user_id' => $leader->user_id, 'status' => 1])->first()
			) {
				return $this->confirmArray($agent);
			}
		}

		return [];
	}

	/**
	 * 活动关联订单
	 *
	 * @param $order_id
	 *
	 * @return array
	 */
	/*protected function checkActivityOrder($order_id)
	{
		if ($acOrder = ActivityOrders::where('order_id', $order_id)->first() AND
			$activity = Activity::find($acOrder->activity_id) AND
			$coach = $activity->coach() AND
			$agent = $this->agent->findWhere(['user_id' => $coach->id, 'status' => 1])->first()
		) {
			return $this->confirmArray($agent);
		}

		return [];
	}*/

	/**
	 * 订单完成 佣金结算
	 *
	 * @param int $order_id 订单id
	 */
	public function agentOrderComplete(Order $order)
	{
		\Log::info('进入佣金结算时间了吗');
		$order       = $this->order->find($order->id);
		$agentOrders = $this->agentOrder->findWhere(['order_id' => $order->id, 'status' => 0]);
		if (count($agentOrders) <= 0) {
			Log::info('该订单不是分销订单, 订单号: ' . $order->order_no);

			return true;
		}
		try {
			DB::beginTransaction();
			foreach ($agentOrders as $agentOrder) {
				$agent_commission = [
					'agent_id'       => $agentOrder->agent_id,
					'agent_order_id' => $agentOrder->id,
					'commission'     => $agentOrder->commission,
					'note'           => '订单: ' . $agentOrder->agent_order_no . ' 结算完成',
				];
				$this->agentCommission->create($agent_commission);

				$agent = $this->agent->find($agentOrder->agent_id);
				/*$balance = [
					'user_id' => $agent->user_id,
					'type' => 'agent',
					'note' => '获得佣金:' . $agentOrder->commission . ' 来源于订单:' . $order->order_no,
					'value' => $agentOrder->commission,
					'origin_id' => $agentOrder->id,
					'origin_type' => 'GuoJiangClub\Distribution\Core\Models\AgentOrder',
				];
				$this->balance->create($balance);*/

				$this->agent->update(['total_commission' => $agentOrder->commission + $agent->total_commission], $agentOrder->agent_id);
				$this->agentOrderItem->updateStatus($agentOrder->id);
				$this->agentOrder->update(['status' => 1, 'settle_time' => Carbon::now()], $agentOrder->id);
			}
			DB::commit();
		} catch (\Exception $exception) {
			DB::rollBack();
			\Log::info('agentOrderComplete' . $exception);
		}

		return true;
	}

	/**
	 * 订单取消
	 *
	 * @param int $order_id 订单id
	 */
	public function agentOrderCanceled($order_id)
	{
		\Log::info('进入订单取消');
		$order = $this->order->find($order_id);
		if (!$order) {
			Log::info('订单不存在,订单id:' . $order_id);

			return;
		}

		$agentOrder = $this->agentOrder->findWhere(['order_id' => $order_id]);
		if (count($agentOrder) == 0) {
			Log::info('该订单不是分销订单,订单id:' . $order_id);

			return;
		}

		\Log::info('订单正常取消，佣金全部失效');
		foreach ($agentOrder as $agent_order) {
			$this->agentOrder->update(['status' => 2], $agent_order->id);

			$agent_order->orderItem()->update(['status' => 2]);
		}
	}

	/**
	 * 分销订单售后，可获得佣金处理
	 *
	 * @param $refund
	 */
	public function agentOrderRefund($refund)
	{
		\Log::info('进入分销售后订单取消');

		$order = $this->order->find($refund->order_id);
		if (!$order OR 6 == $order->status) {
			Log::info('该订单已取消,订单id:' . $refund->order_id);

			return;
		}

		$order_id   = $order->id;
		$agentOrder = $this->agentOrder->findWhere(['order_id' => $order_id])->first();
		if (!$agentOrder) {
			Log::info('该订单不是分销订单,订单id:' . $order_id);

			return;
		}

		$orderItem      = $this->orderItem->find($refund->order_item_id);
		$agentOrderItem = $this->agentOrderItem->findWhere(['order_item_id' => $refund->order_item_id]);
		foreach ($agentOrderItem as $value) {

			$agentOrder = $this->agentOrder->find($value->agent_order_id);

			if ($refund->quantity == $orderItem->quantity) {  //如果申请售后的数量==订单item的数量，佣金失效
				$this->agentOrderItem->update(['status' => 2], $value->id);

				if (count($agentOrder->orderItem) == 1) {  //如果订单只有一个item

					$res = $this->agentOrder->update(['status' => 2], $agentOrder->id);
					Log::info('result:' . json_encode($res));
				} else {  //否则，分销订单的佣金=原佣金-售后的item的佣金
					$currentAgentOrder = $this->agentOrder->find($value->agent_order_id);
					$currentCommission = $currentAgentOrder->commission - $value->commission;
					$this->agentOrder->update(['commission' => $currentCommission], $currentAgentOrder->id);
				}
			} else {
				//更新agent_order_item佣金额
				$percentage     = ($orderItem->quantity - $refund->quantity) / $orderItem->quantity;
				$tmp_commission = $percentage * $value->commission;
				$this->agentOrderItem->update(['commission' => intval($tmp_commission)], $value->id);

				//更新agent_order佣金额
				$currentAgentOrder = $this->agentOrder->find($value->agent_order_id);
				$commission        = $currentAgentOrder->commission - ((1 - $percentage) * $value->commission);
				$this->agentOrder->update(['commission' => intval($commission)], $currentAgentOrder->id);
			}
		}

		$agent = $this->agent->find($agentOrder->agent_id);
		//$agent->user->notify(new AgentOrderRefund(['refund' => $refund]));
	}

	/**
	 * 添加分销商-用户关系
	 *
	 * @param string $agent_code 分销商编码
	 * @param int    $user_id    用户id
	 * @param bool   $is_new     是否是新用户
	 */
	public function createAgentUserRelation($agent_code, $user_id, $is_new = false, $order = null)
	{
		if (!settings('distribution_status')) {
			return;
		}

		if ($order) {
			/*if (!$this->confirmAgentGoods($order, request('agent_goods_id')) OR !settings('distribution_share_relation')) {*/
			if (!$this->confirmAgentGoods($order, request('agent_goods_id'))) {
				return;
			}
		}

		$agentFrom = $this->agent->findWhere(['code' => $agent_code])->first();

		if ($agentFrom) {
			//判断是否给上线分销
			$parents     = $this->agentRelation->findWhere(['agent_id' => $agentFrom->id])->pluck('parent_agent_id')->toArray();
			$parentAgent = $this->agent->findWhere(['user_id' => $user_id])->first();
			if ($parentAgent AND in_array($parentAgent->id, $parents)) {
				return;
			}

			if ($parentAgent) { //如果自身是分销员，不绑定关系
				return;
			}

			$checkExists = $this->agentUserRelation->findWhere(['user_id' => $user_id])->first();
			if (!$checkExists AND $agentFrom->user_id != $user_id AND $agentFrom->status == 1) {
				$agentID = $agentFrom->id;
				$this->agentUserRelation->create([
					'agent_id' => $agentID,
					'user_id'  => $user_id,
					'flag'     => $is_new ? 1 : 2,
				]);
			}
		}
	}

	protected function checkFromAgent($agent_code)
	{
		$flag    = false;
		$agent   = Agent::where('code', $agent_code)->first();
		$agentID = $agent->id;

		/*if ($agent->code == $agent_code) {
			$flag = true;
		} else {
			if (request('shop_id_time') > request('agent_code_time')) {
				$flag    = true;
				$agentID = $agent->id;
			} else {
				$flag = false;
			}
		}*/

		return ['flag' => $flag, 'agentID' => $agentID];
	}

	/**
	 * 用户报名活动，与教练员绑定关系
	 *
	 * @param $activity
	 * @param $user_id
	 */
	public function activityCreateAgentRelation($activity, $user_id)
	{
		$member = $activity->members()->where('role', 'coach')->first();
		if ($member AND $agent = $this->agent->findWhere(['user_id' => $member->user_id, 'status' => 1])->first()) {
			event('agent.user.relation', [$agent->code, $user_id]);
		}
	}

	public function subscribe($events)
	{
		$events->listen(
			'order.submitted',
			'GuoJiangClub\Distribution\Server\Listeners\DistributionEventListener@agentOrderSubmitted'
		);

		$events->listen(
			'agent.commission.state',
			'GuoJiangClub\Distribution\Server\Listeners\DistributionEventListener@agentOrderComplete'
		);

		$events->listen(
			'agent.order.canceled',
			'GuoJiangClub\Distribution\Server\Listeners\DistributionEventListener@agentOrderCanceled'
		);

		$events->listen(
			'agent.order.refund',
			'GuoJiangClub\Distribution\Server\Listeners\DistributionEventListener@agentOrderRefund'
		);

		$events->listen(
			'agent.user.relation',
			'GuoJiangClub\Distribution\Server\Listeners\DistributionEventListener@createAgentUserRelation'
		);

		$events->listen(
			'activity.agent.relation',
			'GuoJiangClub\Distribution\Server\Listeners\DistributionEventListener@activityCreateAgentRelation'
		);
	}
}