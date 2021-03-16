<?php

namespace GuoJiangClub\Distribution\Backend\Models;

use GuoJiangClub\Component\User\Models\User;

class BalanceCash extends \GuoJiangClub\Distribution\Core\Models\BalanceCash
{

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function agent()
	{
		return $this->belongsTo(Agent::class);
	}

	public function getAmountAttribute()
	{
		return $this->attributes['amount'] / 100;
	}

	public function setCertAttribute($value)
	{
		$this->attributes['cert'] = json_encode(explode(';', $value));
	}

	public function getCertAttribute()
	{
		if (!$this->attributes['cert']) {
			return [];
		}

		return json_decode($this->attributes['cert']);
	}

	public function getStatusTextAttribute()
	{
		switch ($this->status) {
			case 0:
				return '待审核';
				break;
			case 1:
				return '待打款';
				break;
			case 2:
				return '已打款';
				break;
			default:
				return '审核未通过';
		}
	}
}