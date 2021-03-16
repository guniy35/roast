<?php

namespace GuoJiangClub\Component\Point\Traits;

use GuoJiangClub\Component\Point\Models\Point;

trait PointTrait
{
	public function points()
	{
		return $this->hasMany(Point::class);
	}
}