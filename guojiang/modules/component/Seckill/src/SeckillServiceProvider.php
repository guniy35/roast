<?php

namespace GuoJiangClub\Component\Seckill;

use Illuminate\Support\ServiceProvider;
use Event;

class SeckillServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}
	}
}
