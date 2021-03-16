<?php

namespace GuoJiangClub\Component\Recharge\Listeners;

use GuoJiangClub\Component\Point\Repository\PointRepository;

class RechargeEventListener
{
	protected $pointRepository;

	public function __construct(PointRepository $pointRepository)
	{
		$this->pointRepository = $pointRepository;
	}

	public function subscribe($events)
	{
		$events->listen(
			'user.recharge.point',
			'GuoJiangClub\Component\Recharge\Listeners\PointEventListener'
		);

		$events->listen(
			'user.recharge.coupon',
			'GuoJiangClub\Component\Recharge\Listeners\CouponEventListener'
		);
	}
}