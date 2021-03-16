<?php

namespace GuoJiangClub\Distribution\Server\Transformers;

use GuoJiangClub\Component\Order\Models\Order;

class CommissionTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		$data = $model->toArray();

		$data['commission'] = number_format($data['commission'] / 100, 2, '.', '');

		$data['agent_order_no'] = $data['agent_order']['agent_order_no'];

		$order_id = $data['agent_order']['order_id'];

		$order_no               = Order::find($order_id)->order_no;
		$data['agent_order_no'] = $order_no;
		//$data['agent_order_no'] = $order_no;
		unset($data['agent_order']);

		return $data;
	}
}