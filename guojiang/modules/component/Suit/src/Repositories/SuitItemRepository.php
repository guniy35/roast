<?php

namespace GuoJiangClub\Component\Suit\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Suit\Models\SuitItems;

class SuitItemRepository extends BaseRepository
{

	const OPEN  = 1;
	const CLOSE = 0;

	/**
	 * Specify Model class name
	 *
	 * @return string
	 */
	public function model()
	{
		return SuitItems::class;
	}

	/**
	 * 通过item_id获取套餐SKU商品的qty
	 *
	 * @param $item_id
	 *
	 * @return mixed
	 */
	public function getSkuQtyByItemId($item_id)
	{
		return $this->findWhere(['item_id' => $item_id, 'item_type' => 'sku', 'status' => self::OPEN])->pluck('quantity')->first();
	}

	/**
	 * 统计套餐原价
	 *
	 * @param $suit_id
	 *
	 * @return int
	 */
	public function getOriginTotal($suit_id)
	{
		$price     = 0;
		$suitItems = $this->with('Suit')->findWhere(['status' => self::OPEN, 'suit_id' => $suit_id]);
		if (count($suitItems) > 0) {
			foreach ($suitItems as $item) {
				$price += $item->origin_price * $item->quantity;
			}
		}

		return $price;
	}

	/**
	 * 统计套餐现价
	 *
	 * @param $suit_id
	 *
	 * @return int
	 */
	public function getTotal($suit_id)
	{
		$price     = 0;
		$suitItems = $this->with('Suit')->findWhere(['status' => self::OPEN, 'suit_id' => $suit_id]);
		if (count($suitItems) > 0) {
			foreach ($suitItems as $item) {
				$price += $item->package_price * $item->quantity;
			}
		}

		return $price;
	}

}
