<?php

namespace GuoJiangClub\EC\Open\Server\Transformers;

class SeckillItemTransformer extends BaseTransformer
{
	public static $excludeable = [
		'deleted_at',
	];

	public function transformData($model)
	{
		$SeckillItem = array_except($model->toArray(), self::$excludeable);

		return $SeckillItem;
	}
}