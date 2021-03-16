<?php

namespace GuoJiangClub\Distribution\Backend\Models;

use GuoJiangClub\Component\Product\Models\Goods;

class AgentGoods extends \GuoJiangClub\Distribution\Core\Models\AgentGoods
{

	public function Goods()
	{
		return $this->belongsTo(Goods::class);
	}

	public function getGoodsCommission($type = 'default')
	{
		$goods = $this->Goods;
		switch ($type) {
			case 'organ':
				$rate = $this->rate_organ / 100;
				break;
			case 'shop':
				$rate = $this->rate_shop / 100;
				break;
			default:
				$rate = $this->rate / 100;
		}
		$min = $rate * $goods->sell_price;
		$max = $rate * $goods->sell_price;
		if ($min_price = $goods->min_price) {
			$min = $rate * $goods->min_price;
		}

		if ($max_price = $goods->max_price) {
			$max = $rate * $goods->max_price;
		}

		if ($min == $max) {
			return $min . '元';
		}

		return $min . ' - ' . $max . '元';
	}

	public function getGoodsSellPriceAttribute()
	{
		$goods = $this->Goods;
		$min   = $goods->sell_price;
		$max   = $goods->sell_price;
		if ($min_price = $goods->min_price) {
			$min = $min_price;
		}

		if ($max_price = $goods->max_price) {
			$max = $max_price;
		}

		if ($min == $max) {
			return '￥ ' . $min;
		}

		return '￥ ' . $min . ' - ￥ ' . $max;
	}

}
