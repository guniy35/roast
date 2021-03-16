<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Repositories\Eloquent;

use GuoJiangClub\EC\Open\Backend\Store\Model\Article;
use GuoJiangClub\EC\Open\Backend\Store\Repositories\ArticleRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class ArticleRepositoryEloquent extends BaseRepository implements ArticleRepository
{
	public function model()
	{
		return Article::class;
	}

	public function getArticlePaginate(array $where, $limit = 15)
	{
		$query = $this->scopeQuery(function ($query) use ($where) {
			if (!empty($where)) {
				foreach ($where as $key => $value) {
					if (is_array($value)) {
						list($condition, $val) = $value;
						$query = $query->where($key, $condition, $val);
					} else {
						$query = $query->where($key, $value);
					}
				}
			}

			return $query->orderBy('created_at', 'DESC');
		});

		if ($limit) {
			return $query->paginate($limit);
		} else {
			return $query->all();
		}
	}
}