<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;

use Carbon\Carbon;
use GuoJiangClub\Component\Seckill\Models\Seckill;
use Prettus\Repository\Eloquent\BaseRepository;

class SeckillRepository extends BaseRepository
{
	public function model()
	{
		return Seckill::class;
	}

	protected function createConditions()
	{
		$where    = [];
		$or_where = [];
		$filter   = [];
		$status   = request('status');

		if ($title = request('title')) {
			$filter['title'] = ['like', '%' . $title . '%'];
		}

		if ($status == 'end') { //已结束,失效
			$where['status']     = 0;
			$or_where['ends_at'] = ['<', Carbon::now()];
		} elseif ($status == 'on') {    //进行中
			$where['status']    = 1;
			$where['starts_at'] = ['<=', Carbon::now()];
			$where['ends_at']   = ['>=', Carbon::now()];
		} elseif ($status == 'future') {    //未开始
			$where['status']    = 1;
			$where['starts_at'] = ['>', Carbon::now()];
		} else {
			$where['status'] = ['<>', 2];
		}

		return [$where, $or_where, $filter];
	}

	/**
	 * 秒杀活动分页数据
	 *
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function getSeckillPaginated($limit = 10)
	{
		$condition = $this->createConditions();
		$where     = $condition[0];
		$or_where  = $condition[1];
		$filter    = $condition[2];

		$data = $this->scopeQuery(function ($query) use ($where, $or_where, $filter) {

			$query = $query->where(function ($query) use ($where, $or_where, $filter) {
				if (count($filter) > 0) {
					foreach ($filter as $key => $value) {
						if (is_array($value)) {
							list($operate, $va) = $value;
							$query = $query->where($key, $operate, $va);
						} else {
							$query = $query->where($key, $value);
						}
					}
				}

				$query->where(function ($query) use ($where, $or_where, $filter) {
					if (count($where) > 0) {
						foreach ($where as $key => $value) {
							if (is_array($value)) {
								list($operate, $va) = $value;
								$query = $query->where($key, $operate, $va);
							} else {
								$query = $query->where($key, $value);
							}
						}
					}
					if (count($or_where) > 0) {
						foreach ($or_where as $key => $value) {
							if (is_array($value)) {
								list($operate, $va) = $value;
								$query = $query->orWhere($key, $operate, $va);
							} else {
								$query = $query->orWhere($key, $value);
							}
						}
					}
				});
			});

			return $query->orderBy('id', 'desc');
		});

		if ($limit == 0) {
			return $data->all();
		} else {
			return $data->paginate($limit);
		}
	}

	/**
	 * 获取有效活动
	 *
	 * @param $id 活动ID
	 *
	 * @return mixed
	 */
	public function getValidSeckill($id)
	{
		return $this->scopeQuery(function ($query) use ($id) {
			$query = $query->where('status', 1)->where('ends_at', '>', Carbon::now());
			if ($id) {
				return $query->where('id', '<>', $id);
			} else {
				return $query;
			}
		})->all();
	}

	/**
	 * 判断所有商品状态是否已经参与有效活动
	 *
	 * @param $goods
	 *
	 * @return mixed
	 */
	public function filterGoodsStatus($goods, $id = 0)
	{
		$seckill = $this->getValidSeckill($id);

		$validIds  = [];
		$goodsData = $goods['data'];

		foreach ($goodsData as $key => $value) {
			$goodsData[$key]['seckill_status'] = 0;
		}

		if (count($seckill) > 0) {
			foreach ($seckill as $item) {
				$goods_ids = $item->items->where('status', 1)->pluck('item_id')->toArray();
				$validIds  = array_merge($validIds, $goods_ids);
			}

			foreach ($goodsData as $key => $value) {
				if (in_array($value['id'], $validIds)) {
					$goodsData[$key]['seckill_status'] = 1;
				}
			}
		}

		$goods['data'] = $goodsData;

		return $goods;
	}

	public function handleUpdateItem($data)
	{
		$updateData = [];
		$createData = [];
		foreach ($data as $key => $item) {
			if (isset($item['id'])) {
				$updateData[] = $item;
			} else {
				$createData[] = $item;
			}
		}

		foreach ($updateData as $key => $value) {
			if (!$value['sell_num']) {
				unset($updateData[$key]['sell_num']);
			}
		}

		return ['updateData' => $updateData, 'createData' => $createData];
	}

}