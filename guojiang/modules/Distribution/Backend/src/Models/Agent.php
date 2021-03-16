<?php

namespace GuoJiangClub\Distribution\Backend\Models;

use GuoJiangClub\Component\User\Models\User;

class Agent extends \GuoJiangClub\Distribution\Core\Models\Agent
{

	public function getMobileAttribute($value)
	{
		return substr_replace($value, '****', 3, 5);
	}

	public function orders()
	{
		return $this->hasMany(AgentOrder::class);
	}

	public function commission()
	{
		return $this->hasMany(AgentCommission::class);
	}

	public function manySubAgents()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		return $this->belongsToMany(Agent::class, $prefix . 'agent_relation', 'parent_agent_id', 'agent_id');
	}

	public function manyUsers()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		return $this->belongsToMany(User::class, $prefix . 'agent_user_relation', 'agent_id', 'user_id');
	}

	public function getAgentStatusAttribute()
	{
		switch ($this->attributes['status']) {
			case 1:
				return '已通过';
				break;
			case 2:
				return '审核未通过';
				break;
			case 3:
				return '已清退';
				break;
			default:
				return '待审核';
		}
	}

	/**
	 * 统计每个层级下线人数量
	 *
	 * @param $level
	 *
	 * @return mixed
	 */
	public function subAgentsCount($level)
	{
		return $this->manySubAgents()->where('level', $level)->count();
	}

	/**
	 * 累计佣金
	 *
	 * @return float
	 */
	public function calculateCash()
	{
		return $this->commission()->sum('commission') / 100;
	}

	/**
	 * 待结算佣金
	 *
	 * @param $status
	 *
	 * @return float
	 */
	public function calculateCommission($status)
	{
		return $this->orders()->where('status', $status)->sum('commission') / 100;
	}

	public function getAgentRoleAttribute()
	{
		switch ($this->type) {
			case 1:
				return '普通推客';
				break;

			case 2:
				return '机构推客';
				break;

			case 3:
				return '门店推客';
				break;

			default:
				return '普通推客';
		}
	}

}