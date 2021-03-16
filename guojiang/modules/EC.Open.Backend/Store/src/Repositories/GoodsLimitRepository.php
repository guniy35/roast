<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\EC\Open\Backend\Store\Model\GoodsLimit;
use GuoJiangClub\Component\Product\Models\Goods;
use Prettus\Repository\Traits\CacheableRepository;

class GoodsLimitRepository extends BaseRepository
{
	use CacheableRepository;

	public function model()
	{
		return GoodsLimit::class;
	}

	public function getGoodsPaginate($where, $ids, $with = '', $limit = 20)
	{
		return $this->scopeQuery(function ($query) use ($where, $ids, $with) {
			if (is_array($where) AND count($where) > 0) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						list($operate, $va) = $value;
						$query = $query->where($key, $operate, $va);
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			if (count($ids)) {
				$query = $query->whereIn('goods_id', $ids);
			}

			if ($with) {
				$query = $query->with($with);
			}

			return $query->orderBy('updated_at', 'desc');
		})->paginate($limit);
	}

	public function getGoodsIdsByCriteria($criteria)
	{
		$ids = Goods::where(function ($query) use ($criteria) {
			if (is_array($criteria) AND count($criteria) > 0) {
				foreach ($criteria as $key => $value) {
					if (is_array($value)) {
						list($operate, $va) = $value;
						$query = $query->where($key, $operate, $va);
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			return $query;
		})->pluck('id')->toArray();

		if (count($ids) > 0) {
			return $ids;
		}

		return [];
	}
}
