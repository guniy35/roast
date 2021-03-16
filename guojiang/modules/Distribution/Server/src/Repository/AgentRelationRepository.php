<?php

namespace GuoJiangClub\Distribution\Server\Repository;

use Prettus\Repository\Eloquent\BaseRepository;
use GuoJiangClub\Distribution\Core\Models\AgentRelation;

class AgentRelationRepository extends BaseRepository
{
	public function model()
	{
		return AgentRelation::class;
	}
}