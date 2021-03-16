<?php

namespace GuoJiangClub\Component\Recharge;

use Illuminate\Support\ServiceProvider;
use Event;

class RechargeServiceProvider extends ServiceProvider
{

	/**
	 * bootstrap, add routes
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}

		Event::subscribe('GuoJiangClub\Component\Recharge\Listeners\RechargeEventListener');
	}

	/**
	 * register the service provider
	 */
	public function register()
	{
		
	}

	public function provides()
	{

	}
}
