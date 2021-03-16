<?php

namespace GuoJiangClub\Distribution\Backend\Models;

class AgentCommission extends \GuoJiangClub\Distribution\Core\Models\AgentCommission
{

	public function agentOrder()
	{
		return $this->belongsTo(AgentOrder::class);
	}

	public function getCommissionAttribute($value)
	{
		return $value / 100;
	}
}