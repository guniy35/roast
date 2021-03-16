<?php

namespace GuoJiangClub\Component\Suit\Providers;

use Illuminate\Support\ServiceProvider;

class SuitServiceProvider extends ServiceProvider
{

	/**
	 * bootstrap, add routes
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../../migrations');
		}
	}
}
