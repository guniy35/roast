<?php

namespace GuoJiangClub\Distribution\Backend\Repository;

use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Distribution\Backend\Models\AgentGoods;
use Prettus\Repository\Eloquent\BaseRepository;

class GoodsRepository extends BaseRepository
{
	public function model()
	{
		return AgentGoods::class;
	}

	public function getGoodsPaginate($where, $ids)
	{
		return $this->scopeQuery(function ($query) use ($where, $ids) {
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

			return $query->orderBy('updated_at', 'desc');
		})->paginate(15);
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