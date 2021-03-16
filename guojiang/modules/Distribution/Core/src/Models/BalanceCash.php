<?php

namespace GuoJiangClub\Distribution\Core\Models;

use GuoJiangClub\Component\Balance\BalanceCash as BaseBalanceCash;

class BalanceCash extends BaseBalanceCash
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'agent_cash');
	}
}