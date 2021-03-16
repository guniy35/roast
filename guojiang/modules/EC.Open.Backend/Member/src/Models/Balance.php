<?php

/*
 * This file is part of ibrand/member-backend.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Backend\Member\Models;

use GuoJiangClub\Component\Balance\Balance as BaseBalance;

class Balance extends BaseBalance
{
	public function setValueAttribute($value)
	{
		$this->attributes['value'] = $value * 100;
	}

	public function user()
	{
		return $this->belongsTo(\GuoJiangClub\Component\User\Models\User::class, 'user_id');
	}
}
