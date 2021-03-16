<?php

namespace GuoJiangClub\Component\Refund;

use GuoJiangClub\Component\Refund\Repositories\Eloquent\RefundRepositoryEloquent;
use GuoJiangClub\Component\Refund\Repositories\RefundRepository;
use Illuminate\Support\ServiceProvider;

class RefundServiceProvider extends ServiceProvider
{
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}
	}

	public function register()
	{
		$this->app->bind(RefundRepository::class, RefundRepositoryEloquent::class);

		$this->app->make('iBrand\Scheduling\ScheduleList')->add(Schedule::class);
	}
}
