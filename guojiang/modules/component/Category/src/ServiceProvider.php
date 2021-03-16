<?php

/*
 * This file is part of ibrand/category.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Category;

use Illuminate\Support\ServiceProvider as LaravelServicePorvider;

class ServiceProvider extends LaravelServicePorvider
{
	/**
	 * Bootstrap the application services.
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}
	}

	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->app->bind(RepositoryContract::class, Repository::class);
	}
}
