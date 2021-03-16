<?php

namespace GuoJiangClub\Component\Refund\Service;

use GuoJiangClub\Component\Refund\Models\RefundLog;

class RefundService
{
	public function refundLog($refund_id, $user_id, $admin_id, $action, $note, $remarks = '')
	{
		return RefundLog::create([
			'refund_id' => $refund_id,
			'user_id'   => $user_id,
			'admin_id'  => $admin_id,
			'action'    => $action,
			'note'      => $note,
			'remark'    => $remarks,
		]);
	}

	/**
	 * 判断订单里面的产品是否全部为完成退款申请
	 *
	 * @param $object
	 * @param $refund
	 *
	 * @return bool
	 */
	public function checkOrderRefund($object, $refund)
	{
		//已完成退款处理的产品
		$filtered = $object->refunds->filter(function ($item) use ($refund) {
			return $item->id !== $refund->id and ($item->status == 8 or $item->status == 3);
		});

		//除开当前处理的退换货，订单中的其他产品
		$filtered2 = $object->items->filter(function ($item) use ($refund) {
			return $item->id !== $refund->order_item_id;
		});

		$num = $filtered->sum('quantity') + $refund->quantity;

		if ((count($filtered) == count($filtered2)) and $num == $object->count) {
			return true;
		}

		return false;
	}
}
