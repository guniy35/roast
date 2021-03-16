<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Distribution\Core\Models\Agent;

class AgentRepository extends BaseRepository
{
	public function model()
	{
		return Agent::class;
	}

	public function getAgentPaginated(array $where, array $agent_ids = [], array $user_ids = [], array $with = [], $limit = 15)
	{
		$query = $this->scopeQuery(function ($query) use ($where, $agent_ids, $user_ids, $with) {
			if (!empty($where)) {
				foreach ($where as $key => $value) {
					if ($key == 'parent_agent_id') {
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

			if (!empty($agent_ids)) {
				$query = $query->whereIn('id', $agent_ids);
			}

			if (!empty($user_ids)) {
				$query = $query->whereIn('user_id', $user_ids);
			}

			if (!empty($with)) {
				foreach ($with as $item) {
					$query = $query->with($item);
				}
			}

			return $query->orderBy('created_at', 'DESC');
		});

		if ($limit) {
			return $query->paginate($limit);
		} else {
			return $query->all();
		}
	}
}