<?php

namespace GuoJiangClub\Distribution\Backend\Models;

use GuoJiangClub\Component\Order\Models\Order;

class AgentOrder extends \GuoJiangClub\Distribution\Core\Models\AgentOrder
{

	public function items()
	{
		return $this->hasMany(AgentOrderItem::class);
	}

	public function order()
	{
		return $this->belongsTo(Order::class)->withDefault(['order_no' => '']);
	}

	public function agent()
	{
		return $this->belongsTo(Agent::class);
	}

	public function fromAgent()
	{
		return $this->hasOne(Agent::class, 'id', 'from_agent_id');
	}

	public function getOrderStatusAttribute()
	{
		switch ($this->order->status) {
			case 1:
				$text = "待付款";
				break;
			case 2:
				$text = "待发货";
				break;
			case 3:
				$text = "配送中";  //或 待收货
				break;
			case 4:
				$text = "待评价";  //已收货待评价
				break;
			case 5:
				$text = "已完成";
				break;
			case 6:
				$text = "已取消";
				break;
			case 7:
				$text = "退款中";
				break;
			case 8:
				$text = "已作废";
				break;
			case 9:
				$text = "已删除";
				break;
			default:
				$text = "";
		}

		return $text;
	}

	public function getTotalCommissionAttribute()
	{
		return $this->attributes['total_commission'] / 100;
	}

	public function getCommissionAttribute()
	{
		return $this->attributes['commission'] / 100;
	}

	public function getCommissionStatusAttribute()
	{
		switch ($this->attributes['status']) {
			case self::STATUS_STATE:
				return '已结算';
				break;
			case self::STATUS_UNSETTLED:
				return '未结算';
				break;
			case self::STATUS_INVALID:
				return '已失效';
				break;
			default:
				return '未结算';
		}
	}

	public function getAgentOrders($order_id)
	{
		return $this->where('order_id', $order_id)->where('order_id', '>', 0)->get();
	}
}