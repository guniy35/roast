<?php

namespace GuoJiangClub\Distribution\Core\Models;

use Illuminate\Database\Eloquent\Model;

class AgentCommission extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'agent_commission');
	}

	public function agentOrder()
	{
		return $this->hasOne('GuoJiangClub\Distribution\Core\Models\AgentOrder', 'id', 'agent_order_id');
	}
}
