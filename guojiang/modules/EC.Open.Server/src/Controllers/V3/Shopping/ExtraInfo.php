<?php

namespace GuoJiangClub\EC\Open\Server\Controllers\V3\Shopping;

use GuoJiangClub\Component\Point\Repository\PointRepository;
use GuoJiangClub\EC\Open\Server\Controllers\V3\Controller;

class ExtraInfo extends Controller
{
	protected $pointRepository;

	public function __construct(PointRepository $pointRepository)
	{
		$this->pointRepository = $pointRepository;
	}

	public function __invoke()
	{
		$user         = request()->user();
		$point        = $this->pointRepository->getSumPointValid($user->id);
		$pointToMoney = settings('point_proportion') ? settings('point_proportion') : 0;
		$pointLimit   = settings('point_order_limit') ? settings('point_order_limit') : 1;

		return $this->success([
			'userPoint'    => $point,
			'pointToMoney' => $pointToMoney / 1,
			'pointLimit'   => $pointLimit / 100,
		]);
	}
}