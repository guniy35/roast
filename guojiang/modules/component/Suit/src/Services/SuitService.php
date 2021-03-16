<?php

namespace GuoJiangClub\Component\Suit\Services;

use GuoJiangClub\Component\Suit\Repositories\SuitItemRepository;
use GuoJiangClub\Component\Suit\Repositories\SuitRepository;

class SuitService
{
	private $suitItemRepository;
	private $suitRepository;

	public function __construct(SuitItemRepository $suitItemRepository, SuitRepository $suitRepository)
	{
		$this->suitItemRepository = $suitItemRepository;
		$this->suitRepository     = $suitRepository;
	}

	/**
	 * 检查订单和套餐信息是否匹配
	 *
	 * @param $goods
	 * @param $pages
	 *
	 * @return bool
	 */
	public function checkOrderSuitInfo($goods, $pages)
	{
		$goods_arr = [];
		$pages_arr = [];
		$must_sku  = [];
		if (!isset($pages->items) || count($goods) == 0) {
			return false;
		}
		$count = count($pages->items);
		foreach ($pages->items as $k => $item) {
			if ($item->item_type == 'spu' && isset($item->item_id)) {
				if ($item->fixed == 1) {
					$pages_arr['fixed'][$k] = $item->item_id . '_' . $item->quantity . '_' . $item->package_price . '_' . '###';
				} else {
					$pages_arr['fixed_no'][$k] = $item->item_id . '_' . $item->quantity . '_' . $item->package_price . '_' . '###';
				}
			}
			if ($item->item_type == 'sku' && isset($item->item_id)) {
				$must_sku[] = $item->item_id;
				if ($item->fixed == 1) {
					$pages_arr['fixed'][$k] = $item->product->goods_id . '_' . $item->quantity . '_' . $item->package_price . '_' . $item->item_id;
				} else {
					$pages_arr['fixed_no'][$k] = $item->product->goods_id . '_' . $item->quantity . '_' . $item->package_price . '_' . $item->item_id;
				}
			}
		}
		$i = 0;
		foreach ($goods as $k => $item) {

			if (isset($item['id']) && !isset($item['attributes']['dynamic_sku']['id'])) {
				$goods_arr[$i] = $item['id'] . '_' . $item['qty'] . '_' . number_format($item['price'], 2, ".", "") . '_' . '###';
				$i++;
			}
			if (isset($item['attributes']['dynamic_sku']['id']) && isset($item['sku_goods_id'])) {
				$sku           = in_array($item['attributes']['dynamic_sku']['id'], $must_sku) ? $item['attributes']['dynamic_sku']['id'] : '###';
				$goods_arr[$i] = $item['sku_goods_id'] . '_' . $item['qty'] . '_' . number_format($item['price'], 2, ".", "") . '_' . $sku;
				$i++;
			}
		}

		if ($count < count($goods_arr) || count($goods_arr) < count($pages_arr['fixed'])) {
			return false;
		}
		$res_fixed = array_diff($goods_arr, $pages_arr['fixed']);

		if (count($res_fixed) > 0) {
			$pages_arr['fixed_no'] = isset($pages_arr['fixed_no']) ? $pages_arr['fixed_no'] : [];
			if (array_diff($res_fixed, $pages_arr['fixed_no'])) {
				return false;
			};
		}

		return true;
	}
}