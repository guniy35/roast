<?php

/*
 * This file is part of ibrand/address.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Address;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->app->bind(RepositoryContract::class, Repository::class);

		$this->app->alias(RepositoryContract::class, 'repository.address');
	}

	/**
	 *  Boot the service provider.
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}
	}
}
