<?php

/*
 * This file is part of ibrand/favorite.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Favorite;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class FavoriteServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}

		$this->publishes([
			__DIR__ . '/../config/favorite.php' => config_path('ibrand/favorite.php'),
		]);

		Relation::morphMap(
			config('ibrand.favorite.models')
		);
	}

	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->app->bind(RepositoryContract::class, Repository::class);
	}
}
