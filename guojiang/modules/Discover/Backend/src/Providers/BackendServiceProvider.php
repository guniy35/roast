<?php

namespace GuoJiangClub\Discover\Backend\Providers;

use GuoJiangClub\Discover\Backend\Console\ImportBackendMenus;
use GuoJiangClub\Discover\Backend\Middleware\Bootstrap;
use Illuminate\Support\ServiceProvider;
use Route;

class BackendServiceProvider extends ServiceProvider
{
	protected $namespace = 'GuoJiangClub\Discover\Backend\Controllers';

	public function boot()
	{
		$this->commands([
			ImportBackendMenus::class,
		]);

		$this->loadViewsFrom(__DIR__ . '/../../resources/views', 'discover-backend');

		$this->map();
	}

	public function register()
	{
		app('router')->aliasMiddleware('discover.bootstrap', Bootstrap::class);
	}

	public function map()
	{
		Route::group(['middleware' => ['web', 'admin'], 'namespace' => $this->namespace, 'prefix' => 'admin'], function ($router) {
			require __DIR__ . '/../routes.php';
		});
	}
}