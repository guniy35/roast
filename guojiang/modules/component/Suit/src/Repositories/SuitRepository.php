<?php

namespace GuoJiangClub\Component\Suit\Repositories;

use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Suit\Models\Suit;
use Prettus\Repository\Traits\CacheableRepository;

class SuitRepository extends BaseRepository
{
	use CacheableRepository;

	const OPEN  = 1;
	const CLOSE = 0;

	/**
	 * Specify Model class name
	 *
	 * @return string
	 */
	public function model()
	{
		return Suit::class;
	}

	/**
	 * @param $where
	 * @param $orWhere
	 *
	 * @return mixed
	 */
	public function getSuitList($where, $orWhere)
	{
		return $this->scopeQuery(function ($query) use ($where, $orWhere) {
			$query = $query->Where(function ($query) use ($where) {
				if (is_array($where)) {
					foreach ($where as $key => $value) {
						if (is_array($value)) {
							list($operate, $va) = $value;
							$query = $query->where($key, $operate, $va);
						} else {
							$query = $query->where($key, $value);
						}
					}
				}
			});

			if (count($orWhere)) {
				$query = $query->orWhere(function ($query) use ($orWhere) {
					if (is_array($orWhere)) {
						foreach ($orWhere as $key => $value) {
							if (is_array($value)) {
								list($operate, $va) = $value;
								$query = $query->where($key, $operate, $va);
							} else {
								$query = $query->where($key, $value);
							}
						}
					}
				});
			}

			return $query->orderBy('created_at', 'desc');
		})->paginate(15);
	}

	/**
	 * 通过GoodsID获取有效套餐信息
	 *
	 * @param $goodsId
	 *
	 * @return mixed
	 */
	public function getSuitByGoodsId($goodsId)
	{
		return $this->model->where('ends_at', '>=', Carbon::now())
			->where('starts_at', '<=', Carbon::now())
			->where('status', self::OPEN)
			->with('items.goods')
			->with('items.product')
			->with(['items' => function ($query) {
				$query->where('status', self::OPEN)->orderBy('sort', 'desc');
			}])
			->whereHas('items', function ($query) use ($goodsId) {
				return $query->where('item_id', $goodsId);
			})->get();
	}

	/**
	 * 通过ID获取有效套餐信息
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getSuitById($id)
	{
		return $this->model->where('ends_at', '>=', Carbon::now())
			->where('starts_at', '<=', Carbon::now())
			->where('status', self::OPEN)
			->with('items.goods')
			->with(['items' => function ($query) {
				$query->where('status', self::OPEN)->orderBy('sort', 'desc');
			}])
			->whereHas('items', function ($query) use ($id) {
				return $query->where('suit_id', $id);
			})->first();
	}

	/**
	 * 获取第一条有效套餐
	 *
	 * @return mixed
	 */
	public function getFirstSuit()
	{
		return $this->model->where('ends_at', '>=', Carbon::now())
			->where('starts_at', '<=', Carbon::now())
			->where('status', self::OPEN)
			->where('recommend', 1)
			->with('items.goods')
			->with('items')
			->whereHas('items', function ($query) {
				$query->where('status', self::OPEN)->orderBy('sort', 'desc');
			})
			->orderBy('id', 'desc')
			->first();
	}

	public function suitList($limit)
	{
		return $this->model->where('ends_at', '>=', Carbon::now())
			->where('starts_at', '<=', Carbon::now())
			->where('status', self::OPEN)
			->with('items.goods')
			->with('items')
			->whereHas('items', function ($query) {
				$query->where('status', self::OPEN)->orderBy('sort', 'desc');
			})
			->orderBy('id', 'desc')
			->paginate($limit);
	}
}
