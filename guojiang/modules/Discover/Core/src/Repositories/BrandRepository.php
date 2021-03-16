<?php

namespace GuoJiangClub\Discover\Core\Repositories;

use GuoJiangClub\EC\Open\Backend\Store\Model\Brand;
use Prettus\Repository\Eloquent\BaseRepository;

class BrandRepository extends BaseRepository
{
	public function model()
	{
		return Brand::class;
	}
}