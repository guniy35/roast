<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface ArticleRepository extends RepositoryInterface
{
	public function getArticlePaginate(array $where, $limit = 15);
}