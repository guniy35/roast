<?php

namespace GuoJiangClub\Discover\Server\Resources;

use GuoJiangClub\Component\Product\Models\Goods;
use iBrand\Common\Resources\BaseResource;
use GuoJiangClub\Discover\Core\Models\DiscoverTag;

class FavoriteResource extends BaseResource
{
	public function toArray($request)
	{
		if (!empty($this->resource->favoriteable->tags_list)) {
			$tags                                    = DiscoverTag::whereIn('id', $this->resource->favoriteable->tags_list)->get(['name']);
			$this->resource->favoriteable->tags_list = $tags->pluck('name')->all();
		}

		if ($this->resource->favoriteable->recommend_goods_id) {
			$this->resource->favoriteable->goods = Goods::where('id', $this->resource->favoriteable->recommend_goods_id)->where('is_del', 0)->first();
		}

		return parent::toArray($request);
	}
}