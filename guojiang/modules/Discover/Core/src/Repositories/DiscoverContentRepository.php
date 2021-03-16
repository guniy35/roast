<?php

namespace GuoJiangClub\Discover\Core\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface DiscoverContentRepository extends RepositoryInterface
{
	public function getDiscoverContentPaginate(array $where, array $with = [], $limit = 15);
}