<?php

namespace GuoJiangClub\Discover\Server\Providers;

use Illuminate\Support\ServiceProvider;

class ServerServiceProvider extends ServiceProvider
{
	public function boot()
	{

	}

	public function register()
	{
		$this->app->register(RouteServiceProvider::class);
	}
}