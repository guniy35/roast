<?php

namespace GuoJiangClub\Discover\Server\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Route;

class RouteServiceProvider extends ServiceProvider
{
	protected $namespace = 'GuoJiangClub\Discover\Server\Controllers';

	public function boot()
	{
		parent::boot();
	}

	public function map()
	{
		Route::group(['middleware' => ['api', 'cors'], 'namespace' => $this->namespace, 'prefix' => 'api'], function ($router) {
			require __DIR__ . '/../routes/api.php';
		});
	}
}