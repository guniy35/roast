<?php

namespace GuoJiangClub\Distribution\Backend\Models;

use GuoJiangClub\Component\Order\Models\OrderItem;

class AgentOrderItem extends \GuoJiangClub\Distribution\Core\Models\AgentOrderItem
{

	public function orderItem()
	{
		return $this->belongsTo(OrderItem::class)->withDefault();
	}

	public function getTotalCommissionAttribute()
	{
		return $this->attributes['total_commission'] / 100;
	}

	public function getCommissionAttribute()
	{
		return $this->attributes['commission'] / 100;
	}
}