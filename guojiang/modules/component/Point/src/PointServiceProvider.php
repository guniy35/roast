<?php

/*
 * This file is part of ibrand/point.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Point;

use GuoJiangClub\Component\Point\Repository\Eloquent\PointRepositoryEloquent;
use GuoJiangClub\Component\Point\Repository\PointRepository;
use Illuminate\Support\ServiceProvider;

class PointServiceProvider extends ServiceProvider
{

	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}
	}

	public function register()
	{
		$this->app->bind(PointRepository::class, PointRepositoryEloquent::class);
	}
}
