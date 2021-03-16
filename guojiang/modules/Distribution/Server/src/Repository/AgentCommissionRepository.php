<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Distribution\Core\Models\AgentCommission;

class AgentCommissionRepository extends BaseRepository
{
	public function model()
	{
		return AgentCommission::class;
	}

	public function getCommissionPaginated($where = [], $with = '', $limit = 15)
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

	public function getSumCommission($agent_id)
	{
		return $this->findWhere(['agent_id' => $agent_id])->sum('commission');
	}
}