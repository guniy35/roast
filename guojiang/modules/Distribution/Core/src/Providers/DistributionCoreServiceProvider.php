<?php

namespace GuoJiangClub\Distribution\Core\Providers;

use GuoJiangClub\Distribution\Core\Schedule;
use Illuminate\Support\ServiceProvider;

class DistributionCoreServiceProvider extends ServiceProvider
{

	public function boot()
	{
		$this->registerMigrations();
	}

	protected function registerMigrations()
	{
		return $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
	}

	public function register()
	{
		$this->app->make('iBrand\Scheduling\ScheduleList')->add(Schedule::class);
	}

}
