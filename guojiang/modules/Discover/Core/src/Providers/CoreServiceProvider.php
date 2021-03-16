<?php

namespace GuoJiangClub\Discover\Core\Providers;

use GuoJiangClub\Discover\Core\Repositories\DiscoverContentRepository;
use GuoJiangClub\Discover\Core\Repositories\Eloquent\DiscoverContentRepositoryEloquent;
use Illuminate\Support\ServiceProvider;
use Event;

class CoreServiceProvider extends ServiceProvider
{
	protected $subscribe = [
	];

	public function boot()
	{
		$this->loadMigrationsFrom(__DIR__ . '/../../migrations');

		foreach ($this->subscribe as $subscriber) {
			Event::subscribe($subscriber);
		}
	}

	public function register()
	{
		$this->app->bind(DiscoverContentRepository::class, DiscoverContentRepositoryEloquent::class);
	}
}