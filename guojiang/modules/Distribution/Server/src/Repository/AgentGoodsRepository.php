<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Distribution\Core\Models\AgentGoods;

class AgentGoodsRepository extends BaseRepository
{
	public function model()
	{
		return AgentGoods::class;
	}

	/**
	 * 获取分销商品列表
	 *
	 * @param array  $where   查询条件
	 * @param string $orderBy 排序字段
	 * @param string $sort    升序|降序
	 * @param int    $limit
	 *
	 * @return mixed
	 */
	public function getGoodsPaginated($where = [], $orderBy, $sort, $limit = 10)
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		return AgentGoods::where($where)
			->join($prefix . 'goods', $prefix . 'goods.id', '=', $prefix . 'agent_goods.goods_id')
			->orderBy($orderBy, $sort)
			->paginate($limit);
	}
}