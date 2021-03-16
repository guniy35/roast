<?php

namespace GuoJiangClub\Distribution\Backend\Providers;

use Illuminate\Support\ServiceProvider;
use Route;
use Menu;
use GuoJiangClub\Distribution\Backend\DistributionBackend;

class DistributionBackendProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'GuoJiangClub\Distribution\Backend\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom(__DIR__ . '/../../resources/views', 'backend-distribution');

		DistributionBackend::boot();

		$this->publishes([
			__DIR__ . '/../../resources/assets' => public_path('assets/backend/distribution'),
		], 'backend-distribution');

		$this->publishes([
			__DIR__ . '/../../resources/assets/libs' => public_path('assets/backend/libs'),
		]);

		$this->map();
		$this->registerMenu();
	}

	public function register()
	{

	}

	public function map()
	{
		Route::group(['middleware' => ['web', 'admin'],
		              'namespace'  => $this->namespace,
		              'prefix'     => 'admin/distribution'], function ($router) {
			require __DIR__ . '/../Http/routes.php';
		});
	}

	private function registerMenu()
	{
		Menu::make('topMenu', function ($menu) {
			$menu->add('<i class="iconfont icon-fenxiaoguanli"></i>
                        <span>分销管理</span>', ['url' => 'admin/distribution', 'secure' => env('SECURE')])->active('admin/distribution/*');
		});
	}

}
