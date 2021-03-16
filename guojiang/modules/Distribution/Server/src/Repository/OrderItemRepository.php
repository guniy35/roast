<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Order\Models\OrderItem;

class OrderItemRepository extends BaseRepository
{
	public function model()
	{
		return OrderItem::class;
	}
}