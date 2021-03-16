<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Distribution\Core\Models\AgentCash;

class AgentCashRepository extends BaseRepository
{
	public function model()
	{
		return AgentCash::class;
	}

	public function getAgentCashPaginated($where = [], $limit = 15)
	{
		$data = $this->scopeQuery(function ($query) use ($where) {
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

			return $query->orderBy('created_at', 'ASC');
		});

		if ($limit == 0) {
			return $data->all();
		} else {
			return $data->paginate($limit);
		}
	}
}