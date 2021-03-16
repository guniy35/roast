<?php

namespace GuoJiangClub\Distribution\Server\Transformers;

class CashTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		$data = $model->toArray();

		$data['amount']     = number_format($data['amount'] / 100, 2, '.', '');
		$data['balance']    = number_format($data['balance'] / 100, 2, '.', '');
		$data['created_at'] = date('Y-m-d', strtotime($data['created_at']));
		$data['updated_at'] = date('Y-m-d', strtotime($data['updated_at']));

		return $data;
	}
}