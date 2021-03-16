<?php

namespace GuoJiangClub\Distribution\Backend\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AgentRelation extends \GuoJiangClub\Distribution\Core\Models\AgentRelation
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public function agent()
	{
		return $this->belongsTo(Agent::class);
	}
}