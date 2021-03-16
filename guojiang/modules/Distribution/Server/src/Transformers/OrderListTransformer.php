<?php

namespace GuoJiangClub\Distribution\Server\Transformers;

class OrderListTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		$data = $model->toArray();

		$data['commission'] = number_format($data['commission'] / 100, 2, '.', '');

		return $data;
	}
}