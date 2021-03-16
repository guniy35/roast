<?php

namespace GuoJiangClub\Component\Recharge\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Recharge\Models\GiftCoupon;

class GiftCouponRepository extends BaseRepository
{
	/**
	 * Specify Model class name
	 *
	 * @return string
	 */
	public function model()
	{
		return GiftCoupon::class;
	}

}
