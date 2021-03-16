<?php

namespace GuoJiangClub\Distribution\Core\Models;

use Illuminate\Database\Eloquent\Model;

class AgentOrder extends Model
{
	const STATUS_STATE     = 1;  //已结算
	const STATUS_UNSETTLED = 0;  //未结算
	const STATUS_INVALID   = 2;    //已失效

	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'agent_order');
	}

	public function orderItem()
	{
		return $this->hasMany('GuoJiangClub\Distribution\Core\Models\AgentOrderItem', 'agent_order_id', 'id');
	}

	public function order()
	{
		return $this->hasOne('GuoJiangClub\Component\Order\Models\Order', 'id', 'order_id');
	}
}