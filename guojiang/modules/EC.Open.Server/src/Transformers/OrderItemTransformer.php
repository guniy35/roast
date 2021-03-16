<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

class OrderItemTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		$model->distribution_status = $model->order->distribution_status;

		if ($model->order->distribution_status == 1 AND $model->is_send == 0) {
			$model->is_send = 1;
		}

		return $model->toArray();
	}

}