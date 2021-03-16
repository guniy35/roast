<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

class SeckillItem extends \GuoJiangClub\Component\Seckill\Models\SeckillItem
{
	public function goods()
	{
		return $this->belongsTo(Goods::class, 'item_id');
	}

	public function sales()
	{
		return $this->hasMany(SeckillSale::class);
	}

	public function getRealSaleAttribute()
	{
		return $this->sales->sum('quantity');
	}
}