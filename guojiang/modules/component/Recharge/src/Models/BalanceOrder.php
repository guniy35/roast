<?php

namespace GuoJiangClub\Component\Recharge\Models;

use GuoJiangClub\Component\User\Models\User;
use GuoJiangClub\Component\Balance\BalanceOrder as BaseBalanceOrder;

class BalanceOrder extends BaseBalanceOrder
{

	public function recharge()
	{
		return $this->hasOne(RechargeRule::class, 'id', 'recharge_rule_id');
	}

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}
}

