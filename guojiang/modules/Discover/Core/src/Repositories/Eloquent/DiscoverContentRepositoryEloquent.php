<?php

namespace GuoJiangClub\Discover\Core\Repositories\Eloquent;

use GuoJiangClub\Discover\Core\Models\DiscoverContent;
use GuoJiangClub\Discover\Core\Repositories\DiscoverContentRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class DiscoverContentRepositoryEloquent extends BaseRepository implements DiscoverContentRepository
{
	public function model()
	{
		return DiscoverContent::class;
	}

	public function getDiscoverContentPaginate(array $where, array $with = [], $limit = 15)
	{
		$query = $this->scopeQuery(function ($query) use ($where, $with) {
			if (!empty($where)) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						list($condition, $val) = $value;
						$query = $query->where($key, $condition, $val);
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			if (!empty($with)) {
				foreach ($with as $item) {
					$query = $query->with($item);
				}
			}

			return $query->orderBy('is_recommend', 'DESC')->orderBy('created_at', 'DESC');
		});

		if ($limit) {
			return $query->paginate($limit);
		} else {
			return $query->all();
		}
	}
}