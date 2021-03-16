<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Component\Refund\Models\Refund;

class RefundRepository extends BaseRepository
{
	public function model()
	{
		return Refund::class;
	}
}