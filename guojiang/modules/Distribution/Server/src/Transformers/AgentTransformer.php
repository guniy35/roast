<?php

namespace GuoJiangClub\Distribution\Server\Transformers;

class AgentTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		$data = $model->toArray();
		
		return $data;
	}
}