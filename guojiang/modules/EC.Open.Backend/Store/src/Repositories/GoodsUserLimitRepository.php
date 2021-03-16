<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\EC\Open\Backend\Store\Model\GoodsUserLimit;

class GoodsUserLimitRepository extends BaseRepository
{
	public function model()
	{
		return GoodsUserLimit::class;
	}
}