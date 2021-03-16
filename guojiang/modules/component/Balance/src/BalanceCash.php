<?php

/*
 * This file is part of ibrand/balance.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Balance;

use GuoJiangClub\Component\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class BalanceCash extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'balance_cash');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
