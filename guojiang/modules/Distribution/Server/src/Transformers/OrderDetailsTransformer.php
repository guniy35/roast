<?php

namespace GuoJiangClub\Distribution\Server\Transformers;

class OrderDetailsTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		$has_refund = 0;
		$refunds    = $model->order->refunds()->where('status', '<>', 3)->get();
		if ($refunds && $refunds->count() > 0) {
			$has_refund = 1;
		}

		$model->has_refund = $has_refund;

		$model->agent_order_no = $model->order->order_no;

		return $model->toArray();
	}
}