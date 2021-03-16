<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Distribution\Core\Models\AgentOrderItem;
use DB;

class AgentOrderItemRepository extends BaseRepository
{
	public function model()
	{
		return AgentOrderItem::class;
	}

	public function getOrderItemPaginated($where = [], $with = '', $limit = 15)
	{
		$data = $this->scopeQuery(function ($query) use ($where, $with) {
			if (is_array($where) && !empty($where)) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						list($operate, $va) = $value;
						$query = $query->where($key, $operate, $va);
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			if ($with) {
				$query = $query->with($with);
			}

			return $query->orderBy('created_at', 'DESC');
		});

		if ($limit == 0) {
			return $data->all();
		} else {
			return $data->paginate($limit);
		}
	}

	public function updateStatus($agent_order_id)
	{
		return DB::table('ibrand_agent_order_item')
			->where('agent_order_id', $agent_order_id)
			->where('status', 0)
			->update([
				'status'      => AgentOrderItem::STATUS_STATE,
				'settle_time' => Carbon::now(),
			]);
	}
}