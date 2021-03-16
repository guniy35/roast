<?php

namespace GuoJiangClub\Discover\Server\Resources;

use GuoJiangClub\Discover\Core\Models\DiscoverContent;
use GuoJiangClub\Discover\Core\Models\DiscoverTag;
use iBrand\Common\Resources\BaseResource;
use GuoJiangClub\Component\Favorite\Favorite;

class ContentResource extends BaseResource
{
	public function toArray($request)
	{
		$user        = auth('api')->user();
		$is_favorite = 0;
		if ($user) {
			$favorite = Favorite::where(['user_id' => $user->id, 'favoriteable_id' => $this->resource->id, 'favoriteable_type' => DiscoverContent::class])->first();
			if ($favorite) {
				$is_favorite = 1;
			}
		}

		$this->resource->is_favorite = $is_favorite;

		$this->resource->favorite_count = Favorite::where(['favoriteable_id' => $this->resource->id, 'favoriteable_type' => DiscoverContent::class])->count();

		if (empty($this->resource->goods)) {
			$this->resource->goods = null;
		}

		if (!empty($this->resource->tags_list)) {
			$tags                      = DiscoverTag::whereIn('id', $this->resource->tags_list)->get(['name']);
			$this->resource->tags_list = $tags->pluck('name')->all();
		}

		return parent::toArray($request);
	}
}