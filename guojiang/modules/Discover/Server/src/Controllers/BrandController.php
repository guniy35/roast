<?php

namespace GuoJiangClub\Discover\Server\Controllers;

use GuoJiangClub\Component\Product\Repositories\GoodsRepository;
use GuoJiangClub\Discover\Core\Repositories\BrandRepository;
use GuoJiangClub\Discover\Server\Resources\GoodsResource;
use iBrand\Common\Controllers\Controller;

class BrandController extends Controller
{
	protected $brandRepository;
	protected $goodsRepository;

	public function __construct(BrandRepository $brandRepository, GoodsRepository $goodsRepository)
	{
		$this->brandRepository = $brandRepository;
		$this->goodsRepository = $goodsRepository;
	}

	public function list($id)
	{
		$brand = $this->brandRepository->findWhere(['is_show' => 1, 'id' => $id])->first();
		if (!$brand) {
			return $this->failed('品牌不存在');
		}

		$limit = request('limit') ? request('limit') : 15;

		$commend_goods     = $this->goodsRepository->findWhere(['is_commend' => 1, 'brand_id' => $id, 'is_del' => 0]);
		$commend_goods_ids = [];
		if ($commend_goods->count() > 0) {
			$commend_goods_ids = $commend_goods->pluck('id')->all();
		}

		$goods_list = $this->goodsRepository->scopeQuery(function ($query) use ($commend_goods_ids, $id) {
			$query = $query->where('is_del', 0)->where('brand_id', $id);

			if (!empty($commend_goods_ids)) {
				$query = $query->whereNotIn('id', $commend_goods_ids);
			}

			return $query->orderBy('created_at', 'DESC');
		})->paginate($limit);

		return $this->paginator($goods_list, GoodsResource::class, ['commend_goods' => $commend_goods, 'brand' => $brand]);
	}

	public function detail($id)
	{
		$brand = $this->brandRepository->findWhere(['is_show' => 1, 'id' => $id])->first();
		if (!$brand) {
			return $this->failed('品牌不存在');
		}

		return $this->success(['detail' => $brand]);
	}
}