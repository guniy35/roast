<?php

namespace GuoJiangClub\Distribution\Backend\Repository;

use GuoJiangClub\Distribution\Backend\Models\AgentOrder;
use Prettus\Repository\Eloquent\BaseRepository;

class OrdersRepository extends BaseRepository
{
	public function model()
	{
		return AgentOrder::class;
	}

	public function getAgentOrdersPaginate($where, $time, $limit = 15)
	{
		return $this->scopeQuery(function ($query) use ($where, $time) {
			if (is_array($where) AND count($where) > 0) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						if ($key == 'order_id' OR $key == 'agent_id') {
							$query = $query->whereIn($key, $value);
						} else {
							list($operate, $va) = $value;
							$query = $query->where($key, $operate, $va);
						}
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			/*时间*/
			if (is_array($time) AND isset($time['created_at'])) {
				$query = $query->whereBetween('created_at', $time['created_at']);
			}

			return $query->orderBy('created_at', 'desc');
		})->paginate($limit);
	}

	public function formatToExcelData($order)
	{
		$data = [];
		if ($order AND count($order) > 0) {
			$i = 0;
			foreach ($order as $item) {
				$data[$i][] = $item->agent_order_no;
				$data[$i][] = $item->order->order_no;
				$data[$i][] = $item->order_status;
				$data[$i][] = $item->order->created_at;
				$data[$i][] = $item->order->pay_time;
				$data[$i][] = $item->agent->name;
				$data[$i][] = $item->agent->mobile;
				$data[$i][] = $item->commission;
				$data[$i][] = $item->CommissionStatus;
				$data[$i][] = $item->settle_time;

				$refund_status = '否';
				if ($refund = $item->order->refunds->first()) {
					$refund_status = '是';
				}
				$data[$i][] = $refund_status;

				$goods      = '';
				$orderItems = $item->order->items;
				foreach ($orderItems as $val) {
					$goods = $goods . $val->item_name . '（' . $val->item_meta['specs_text'] . '）；';
				}
				$data[$i][] = $goods;

				$payments = $item->order->payments;
				if ($payments) {
					$cash    = $payments->where('channel', '<>', 'balance')->first();
					$balance = $payments->where('channel', 'balance')->first();
					if ($cash) {
						$data[$i][] = $cash->amount_yuan;
					} else {
						$data[$i][] = 0;
					}

					if ($balance) {
						$data[$i][] = $balance->amount_yuan;
					} else {
						$data[$i][] = 0;
					}
				} else {
					$data[$i][] = 0;
					$data[$i][] = 0;
				}

				$i++;
			}
		}

		return $data;
	}

	/**
	 * 根据时间统计分销订单相关数据
	 *
	 * @param      $time
	 * @param bool $isCountAmount 是否统计金额
	 *
	 * @return float
	 */
	public function getAgentOrderData($time, $isCountAmount = true)
	{
		$data = $this->scopeQuery(function ($query) use ($time) {
			return $query->where('level', 1)
				->whereHas('order', function ($query) use ($time) {
					$query = $query->where('status', '>', 0);
					if (count($time) > 0) {
						$query->whereBetween('created_at', $time);
					}
				});
		})->with('order')->all();

		if ($isCountAmount) {
			return $data->sum('order.total') / 100;
		}

		return $data->count();
	}

	/**
	 * 统计待结算金额
	 *
	 * @param $time
	 */
	public function getAgentOrderCommissionData($time)
	{
		$data = $this->scopeQuery(function ($query) use ($time) {
			$query = $query->where('status', 0);
			if (count($time) > 0) {
				$query->whereBetween('created_at', $time);
			}

			return $query;
		})->all();

		return $data->sum('commission');
	}

}