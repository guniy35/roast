<?php

namespace GuoJiangClub\EC\Open\Backend\Store\Model;

use Carbon\Carbon;

class Seckill extends \GuoJiangClub\Component\Seckill\Models\Seckill
{

	public function items()
	{
		return $this->hasMany(SeckillItem::class);
	}

	public function getStatusTextAttribute()
	{
		if ($this->status == 1 AND $this->ends_at > Carbon::now() AND $this->starts_at <= Carbon::now()) {
			return '进行中';
		} elseif ($this->status == 1 AND $this->starts_at > Carbon::now()) {
			return '未开始';
		} else {
			return '已结束';
		}
	}

	/**
	 * 判断活动是否已进行
	 *
	 * @return int
	 */
	public function getCheckStatusAttribute()
	{
		if ($this->status == 1 AND $this->ends_at > Carbon::now() AND $this->starts_at <= Carbon::now()) {
			return 1; //进行中
		} elseif ($this->ends_at < Carbon::now() OR $this->status == 0 OR $this->status == 2) { //已结束,失效，删除
			return 2;
		}

		return 0; //未开始
	}
}