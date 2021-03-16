<?php

/*
 * This file is part of ibrand/EC-Open-Server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Server\Transformers;

use DB;
use GuoJiangClub\Component\Order\Repositories\CommentRepository;
use GuoJiangClub\Distribution\Core\Models\Agent;
use GuoJiangClub\Distribution\Server\Services\AgentsService;

class GoodsTransformer extends BaseTransformer
{
	protected $type;

	public function __construct($type = 'detail')
	{
		$this->type = $type;
	}

	/**
	 * List of resources possible to include.
	 *
	 * @var array
	 */
	protected $availableIncludes = [
		'products', 'photos', 'oneComment',
	];

	public static $excludeable = [
		'content',
	];

	public function transformData($model)
	{
		$tags = explode(',', $model->tags);

		$model->tags             = '' == $tags[0] ? [] : $tags;
		$goods                   = $model->toArray();
		$goods['is_agent_goods'] = false;

		if ('detail' == $this->type) {
			$goods['shop_hidden_more_info'] = env('SHOP_HIDDEN_MORE_INFO') ? 1 : 0;
			$goods['shop_show_sell_nums']   = settings('shop_show_sell_nums') ? 1 : 0;
			$distribution_status            = settings('distribution_status');
			$goods['can_share']             = false;
			$goods['agent_code']            = '';
			if ($distribution_status) {
				$prefix = config('ibrand.app.database.prefix', 'ibrand_');
				$search = DB::table($prefix . 'agent_goods')->where('goods_id', $goods['id'])->first();
				if ($search and 1 == $search->activity) {
					$goods['is_agent_goods'] = true;

					if ($user = auth('api')->user() and $agent = Agent::where(['status' => 1, 'user_id' => $user->id])->first()) {
						$goods['can_share']  = true;
						$goods['agent_code'] = $agent->code;
						$goods['commission'] = app(AgentsService::class)->getCommissionByGoodsID($model);

						/*猫大不显示佣金*/
						$goods['show_commission'] = true;
						if (env('MAODA_COMMISSION')) {
							$goods['show_commission'] = false;
						}
					}
				}
			}
		}

		if ('list' == $this->type) {
			return array_except($goods, self::$excludeable);
		}

		return $goods;
	}

	public function includePhotos($model)
	{
		$photos = $model->photos()->orderBy('is_default', 'desc')->orderBy('sort', 'desc')->get();

		return $this->collection($photos, new GoodsPhotoTransformer(), '');
	}

	public function includeProducts($model)
	{
		$products = $model->products->filter(function ($item) {
			return $item->store_nums > 0;
		});

		return $this->collection($products, new ProductTransformer(), '');
	}

	public function includeOneComment($model)
	{
		$commentRepository = app(CommentRepository::class);

		$comments = $commentRepository->getRecommendByItem($model->id)->take(1);

		return $this->collection($comments, new CommentTransformer(), '');
	}
}

class GoodsPhotoTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		return $model->toArray();
	}
}

class ProductTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		return $model->toArray();
	}
}
