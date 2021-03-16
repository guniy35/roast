<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Order\Models\Order;

class OrderRepository extends BaseRepository
{
	public function model()
	{
		return Order::class;
	}
}