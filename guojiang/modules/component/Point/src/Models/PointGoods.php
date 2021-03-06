<?php

namespace GuoJiangClub\Component\Point\Models;

use Illuminate\Database\Eloquent\Model;

class PointGoods extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'point_goods');
	}

	public function scopeOfItem($query, $itemId)
	{
		return $query->where('item_id', $itemId);
	}

	/**
	 * 根据金额获取可获得的积分具体数值
	 *
	 * @param $total
	 */
	public function getPoint($total, $quantity = 1)
	{
		if ($this->type == 0) {
			return $this->value * $quantity;
		} else {
			return $this->value * $total / 10000;
		}
	}
}
