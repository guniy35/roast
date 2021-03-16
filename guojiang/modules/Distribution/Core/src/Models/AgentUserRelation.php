<?php

namespace GuoJiangClub\Distribution\Core\Models;

use Illuminate\Database\Eloquent\Model;

class AgentUserRelation extends Model
{
	protected $guarded = ['id'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'agent_user_relation');
	}

	public function user()
	{
		return $this->hasOne('GuoJiangClub\Component\User\Models\User', 'id', 'user_id');
	}

	public function user_bind()
	{
		return $this->hasOne('GuoJiangClub\Component\User\Models\UserBind', 'user_id', 'user_id');
	}
}