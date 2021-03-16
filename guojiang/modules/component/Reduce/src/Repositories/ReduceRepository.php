<?php

/*
 * This file is part of ibrand/reduce.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Reduce\Repositories;

use Carbon\Carbon;
use GuoJiangClub\Component\Reduce\Models\Reduce;
use Prettus\Repository\Eloquent\BaseRepository;

class ReduceRepository extends BaseRepository
{
	const OPEN = 1;

	const CLOSE = 0;

	/**
	 * Specify Model class name.
	 *
	 * @return string
	 */
	public function model()
	{
		return Reduce::class;
	}

	/**
	 * 获取全部进行中有效砍价信息
	 *
	 * @param int $limit
	 *
	 * @return mixed
	 */
	public function getReduceList($limit = 0)
	{
		$model = $this->model
			->where('store_nums', '>', 0)
			->where('ends_at', '>=', Carbon::now())
			->where('starts_at', '<=', Carbon::now())
			->where('status', self::OPEN)
			->with('goods')
			->orderBy('sort', 'desc');

		if ($limit) {
			return $model->paginate($limit);
		}

		return $model->get();
	}
}
