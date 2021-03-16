<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Listeners;

use GuoJiangClub\EC\Open\Backend\Store\Model\PromotionGoodsRelation;
use GuoJiangClub\EC\Open\Backend\Store\Model\Refund;

class PromotionEventListener
{
	/**
	 * 促销活动创建，将商品与活动关系记录
	 *
	 * @param Refund $refund
	 * @param        $order_id
	 */
	public function onPromotionCreated($goods_id, $origin_type, $origin_id)
	{
		if (is_array($goods_id)) {
			foreach ($goods_id as $item) {
				PromotionGoodsRelation::create([
					'goods_id'    => $item,
					'origin_type' => $origin_type,
					'origin_id'   => $origin_id,
				]);
			}
		} else {
			PromotionGoodsRelation::create([
				'goods_id'    => $goods_id,
				'origin_type' => $origin_type,
				'origin_id'   => $origin_id,
			]);
		}
	}

	/**
	 * 活动失效或者删除，取消商品与活动的绑定关系
	 *
	 * @param $goods_id
	 * @param $origin_type
	 * @param $origin_id
	 */
	public function onPromotionDeleted($goods_id, $origin_type, $origin_id)
	{
		if ($relation = PromotionGoodsRelation::where([
			'goods_id'    => $goods_id,
			'origin_type' => $origin_type,
			'origin_id'   => $origin_id,
		])->first()
		) {
			$relation->delete();
		}
	}

	public function subscribe($events)
	{
		$events->listen(
			'promotion.created',
			'GuoJiangClub\EC\Open\Backend\Store\Listeners\PromotionEventListener@onPromotionCreated'
		);

		$events->listen(
			'promotion.deleted',
			'GuoJiangClub\EC\Open\Backend\Store\Listeners\PromotionEventListener@onPromotionDeleted'
		);
	}
}