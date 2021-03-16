<?php

namespace GuoJiangClub\Discover\Server\Controllers;

use GuoJiangClub\Discover\Server\Resources\FavoriteResource;
use iBrand\Common\Controllers\Controller;
use GuoJiangClub\Component\Favorite\Repository;
use GuoJiangClub\Discover\Core\Models\DiscoverContent;

class FavoriteController extends Controller
{
	protected $favoriteRepository;

	public function __construct(Repository $repository)
	{
		$this->favoriteRepository = $repository;
	}

	public function index()
	{
		$user  = request()->user();
		$limit = request('limit');
		$data  = $this->favoriteRepository->getByUserAndType($user->id, DiscoverContent::class, $limit);

		return $this->paginator($data, FavoriteResource::class);
	}

	public function addFavorite()
	{
		$id      = request('content_id');
		$content = DiscoverContent::where('status', 1)->where('id', $id)->first();
		if (!$content) {
			return $this->failed('内容不存在');
		}

		$user = request()->user();
		$this->favoriteRepository->add($user->id, $id, DiscoverContent::class);

		return $this->success();
	}

	public function deleteFavorite()
	{
		$user = request()->user();
		$id   = request('content_id');
		if (empty($id)) {
			return $this->failed('内容不存在');
		}

		if (!is_array($id)) {
			$ids[] = $id;
		} else {
			$ids = $id;
		}

		$this->favoriteRepository->delFavorites($user->id, $ids);

		return $this->success();
	}
}