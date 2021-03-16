<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Distribution\Core\Models\AgentOrder;

class AgentOrderRepository extends BaseRepository
{
	public function model()
	{
		return AgentOrder::class;
	}

	public function getOrderDetailsPaginated($agent_id, $status, $limit = 15)
	{
		if ($status) {
			$res = AgentOrder::where('agent_id', $agent_id)
				->join('ibrand_order', function ($join) use ($status) {
					$join->on('ibrand_agent_order.order_id', '=', 'ibrand_order.id')
						->where('ibrand_order.status', '=', $status);
				});
		} else {
			$res = AgentOrder::where('agent_id', $agent_id)
				->join('ibrand_order', 'ibrand_agent_order.order_id', '=', 'ibrand_order.id');
		}

		return $res->with('order')->with('order.items')->orderBy('ibrand_agent_order.created_at', 'DESC')->select('ibrand_agent_order.*')->paginate($limit);
	}

	public function getMiniProgramOrderDetailsPaginated($status, array $agent_ids = [], $limit = 15)
	{
		$query = $this->scopeQuery(function ($query) use ($status, $agent_ids) {
			if (!empty($agent_ids)) {
				$query = $query->whereIn('agent_id', $agent_ids);
			}

			if ($status && $status != 4) {
				$query = $query->whereHas('order', function ($query) use ($status) {
					$query->where('status', $status);
				});
			}

			if ($status == 4) {
				$query = $query->whereHas('order.refunds', function ($query) use ($status) {
					$query->where('status', '<>', 3);
				});
			}

			return $query->with('order')->with('order.items')->orderBy('created_at', 'DESC');
		});

		if ($limit) {
			return $query->paginate($limit);
		} else {
			return $query->all();
		}
	}

	public function getAgentOrderPaginated(array $where, $limit = 10)
	{
		$query = $this->scopeQuery(function ($query) use ($where) {
			if (!empty($where)) {
				foreach ($where as $key => $value) {
					if ($key == 'agent_ids') {
						continue;
					}

					if (is_array($value)) {
						list($operate, $va) = $value;
						$query = $query->where($key, $operate, $va);
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			if (isset($where['agent_ids']) && !empty($where['agent_ids'])) {
				$query = $query->whereIn('agent_id', $where['agent_ids']);
			}

			return $query->with('order')->with('order.items')->orderBy('created_at', 'DESC');
		});

		if ($limit) {
			return $query->paginate($limit);
		} else {
			return $query->all();
		}
	}
}