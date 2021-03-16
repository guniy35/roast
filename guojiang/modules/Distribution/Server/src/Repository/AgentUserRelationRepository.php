<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Distribution\Core\Models\AgentUserRelation;

class AgentUserRelationRepository extends BaseRepository
{
	public function model()
	{
		return AgentUserRelation::class;
	}

	public function getUserPaginated($where = [], $with = '', array $agent_ids = [], $limit = 15)
	{
		$data = $this->scopeQuery(function ($query) use ($where, $with, $agent_ids) {
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

			if (!empty($agent_ids)) {
				$query = $query->whereIn('agent_id', $agent_ids);
			}

			return $query->orderBy('created_at', 'DESC');
		});

		if ($limit == 0) {
			return $data->all();
		} else {
			return $data->paginate($limit);
		}
	}
}