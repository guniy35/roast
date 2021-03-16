<?php

namespace GuoJiangClub\Distribution\Server\Transformers;

use DB;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Component\Order\Models\Comment;

class GoodsListTransformer extends BaseTransformer
{
	/**
	 * List of resources possible to include
	 *
	 * @var array
	 */
	protected $availableIncludes = [
		'products', 'photos', 'oneComment', 'guessYouLike', 'whoLike', 'point',
	];

	public function transformData($model)
	{
		$tags        = explode(',', $model->tags);
		$model->tags = $tags[0] == '' ? [] : $tags;
		$goods       = $model->toArray();

		return $goods;
	}

	/**
	 * Include Group
	 *
	 * @return
	 */
	public function includePhotos($model)
	{
		$photos = $model->photos()->where('flag', 1)->orderBy('is_default', 'desc')->orderBy('sort', 'desc')->get();

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
		$comments = $model->comments()->where('status', Comment::STATUS_SHOW)->with('user')->orderBy('recommend', 'desc')->orderBy('created_at', 'desc')->take(1)->get();

		return $this->collection($comments, new CommentTransformer(), '');
	}

	public function includeGuessYouLike($model)
	{
		$cid        = [];
		$categories = $model->categories;
		foreach ($categories as $category) {
			$cid[] = $category->id;
		}
		$categoryGoodsIds = DB::table('ibrand_goods_category')->whereIn('category_id', $cid)->select('goods_id')->distinct()->get()
			->pluck('goods_id')->toArray();
		$goods            = Goods::select(['id', 'name', 'sell_price', 'img'])->whereIn('id', $categoryGoodsIds)->get();
		$goods            = $goods->shuffle()->take(3);

		return $this->collection($goods, new GuessYouLikeTransformer(), '');
	}

	public function includeWhoLike($model)
	{
		$pid      = [];
		$products = $model->products;
		foreach ($products as $product) {
			$pid[] = $product->id;
		}
		$orderIds = DB::table('ibrand_order_item')->where('type', 'GuoJiangClub\Component\Product\Models\Product')
			->whereIn('item_id', $pid)->select('order_id')->distinct()->get();
		$orderIds = $orderIds->shuffle()->take(4)->pluck('order_id')->toArray();

		$orderUserIds = DB::table('ibrand_order')->whereIn('id', $orderIds)->select('user_id')->distinct()->get()
			->pluck('user_id')->toArray();

		$users = User::select(['id', 'nick_name', 'avatar', 'group_id'])->whereIn('id', $orderUserIds)->get();

		return $this->collection($users, new WhoLikeTransformer(), '');
	}

	public function includePoint($model)
	{
		$point = $model->hasOnePoint;
		if ($point) {
			if ($point->type == 1) {
				$point->value = $point->value * $model->sell_price / 100;
			}

			return $this->item($point, new PointTransformer(), '');
		}
	}
}