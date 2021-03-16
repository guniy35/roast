<?php

namespace GuoJiangClub\Component\Marketing\Providers;

use GuoJiangClub\Component\Marketing\Repositories\Eloquent\SignItemRepositoryEloquent;
use GuoJiangClub\Component\Marketing\Repositories\SignItemRepository;
use Illuminate\Support\ServiceProvider;

class MarketingServiceProvider extends ServiceProvider
{

	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../../migrations');
		}
	}

	public function register()
	{
		$this->app->bind(SignItemRepository::class, SignItemRepositoryEloquent::class);
	}
}