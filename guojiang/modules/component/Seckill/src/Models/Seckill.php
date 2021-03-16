<?php

namespace GuoJiangClub\Component\Seckill\Models;

use Illuminate\Database\Eloquent\Model;

class Seckill extends Model
{
	protected $guarded = ['id'];

	protected $appends = ['tag'];

	public function __construct(array $attributes = [])
	{
		$this->setTable(config('ibrand.app.database.prefix', 'ibrand_') . 'seckill');

		parent::__construct($attributes);
	}

	public function items()
	{
		return $this->hasMany(SeckillItem::class);
	}

	public function getTagAttribute()
	{
		if (!empty($this->tags)) {
			return $res = explode(',', $this->tags);
		}

		return '';
	}

}