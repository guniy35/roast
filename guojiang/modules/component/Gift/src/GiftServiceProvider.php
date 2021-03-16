<?php

namespace GuoJiangClub\Component\Gift;

use Illuminate\Support\ServiceProvider;
use Event;

class GiftServiceProvider extends ServiceProvider
{

	/**
	 * bootstrap, add routes
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}

		Event::subscribe('GuoJiangClub\Component\Gift\Listeners\NewUser\GiftEventListener');
		Event::subscribe('GuoJiangClub\Component\Gift\Listeners\Birthday\GiftEventListener');
		Event::subscribe('GuoJiangClub\Component\Gift\Listeners\DirectionalCoupon\GiftEventListener');
	}

	/**
	 * register the service provider
	 */
	public function register()
	{
		$this->app->make('iBrand\Scheduling\ScheduleList')->add(Schedule::class);
	}

}
