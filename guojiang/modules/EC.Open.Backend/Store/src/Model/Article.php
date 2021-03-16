<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	protected $guarded = ['id'];

	const TYPE_STARS_RECOMMEND = 1; //明星大咖推荐
	const TYPE_EXCLUSIVE_CASES = 2; //专属方案

	const STATUS_INVALID = 0; //下架
	const STATUS_VALID   = 1; //发布

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		$this->setTable($prefix . 'article');
	}

	public function getTypeTextAttribute()
	{
		$text = '';
		switch ($this->type) {
			case self::TYPE_STARS_RECOMMEND :
				$text = '明星大咖推荐';
				break;
			case self::TYPE_EXCLUSIVE_CASES :
				$text = '专属方案';
				break;
		}

		return $text;
	}
}