<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Product\Models\Goods;

class GoodsRepository extends BaseRepository
{
	public function model()
	{
		return Goods::class;
	}
}