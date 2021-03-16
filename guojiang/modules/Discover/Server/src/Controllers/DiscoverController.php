<?php

namespace GuoJiangClub\Discover\Server\Controllers;

use GuoJiangClub\EC\Open\Backend\Store\Model\Brand;
use GuoJiangClub\Discover\Core\Models\DiscoverBanner;
use GuoJiangClub\Discover\Core\Models\DiscoverCategory;
use GuoJiangClub\Discover\Core\Models\DiscoverTag;
use GuoJiangClub\Discover\Core\Repositories\DiscoverContentRepository;
use GuoJiangClub\Discover\Server\Resources\ContentResource;
use iBrand\Common\Controllers\Controller;

class DiscoverController extends Controller
{
	protected $contentRepository;

	public function __construct(DiscoverContentRepository $contentRepository)
	{
		$this->contentRepository = $contentRepository;
	}

	public function banners()
	{
		$banners = DiscoverBanner::where('status', 1)->get();

		return $this->success(['banners' => $banners]);
	}

	public function conditions()
	{
		$brands = Brand::where('is_show', 1)->get(['id', 'name']);

		$tags = DiscoverTag::where('status', 1)->get(['id', 'name']);

		$categories = DiscoverCategory::where('status', 1)->get(['id', 'name']);

		return $this->success(['brands' => $brands, 'tags' => $tags, 'categories' => $categories]);
	}

	public function list()
	{
		$limit = request('limit') ? request('limit') : 15;

		$where = $this->getConditions();

		$list = $this->contentRepository->getDiscoverContentPaginate($where, ['goods'], $limit);

		return $this->paginator($list, ContentResource::class);
	}

	public function getConditions()
	{
		$where['status'] = 1;
		if (request('brand_id')) {
			$where['brand_id'] = request('brand_id');
		}

		if (request('category_id')) {
			$where['discover_category_id'] = request('category_id');
		}

		if (request('tags')) {
			$where['tags_list'] = ['like', '%' . str_replace(',', '%', request('tags')) . '%'];
		}

		if (request('keywords')) {
			$where['description'] = ['like', '%' . request('keywords') . '%'];
		}

		return $where;
	}
}