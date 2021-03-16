<?php

namespace GuoJiangClub\Distribution\Backend\Models;

class AgentCash extends \GuoJiangClub\Distribution\Core\Models\AgentCash
{

	public function agent()
	{
		return $this->belongsTo(Agent::class);
	}

	public function getAmountAttribute()
	{
		return $this->attributes['amount'] / 100;
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