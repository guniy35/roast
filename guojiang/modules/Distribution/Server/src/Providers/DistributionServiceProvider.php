<?php

namespace GuoJiangClub\Distribution\Server\Providers;

use GuoJiangClub\Distribution\Server\Console\GetUserTokenCommand;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use GuoJiangClub\EC\Open\Server\Serializer\DataArraySerializer;
use Dingo\Api\Transformer\Adapter\Fractal;
use League\Fractal\Manager;
use Event;

class DistributionServiceProvider extends RouteServiceProvider
{

	/**
	 * 要注册的订阅者类。
	 *
	 * @var array
	 */
	protected $subscribe = [
		'GuoJiangClub\Distribution\Server\Listeners\DistributionEventListener',
	];

	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'GuoJiangClub\Distribution\Server\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();

		$this->app['Dingo\Api\Transformer\Factory']->setAdapter(function ($app) {
			$fractal = new Manager();
			$fractal->setSerializer(new DataArraySerializer());

			return new Fractal($fractal);
		});

		foreach ($this->subscribe as $item) {
			Event::subscribe($item);
		}

		$this->loadViewsFrom(__DIR__ . '/../../resources/views', 'distribution-server');

		$this->commands([
			GetUserTokenCommand::class,
		]);
	}

	public function register()
	{
	}

	public function map()
	{
		$api = app('Dingo\Api\Routing\Router');
		$api->version('v3',
			['middleware' => ['api', 'cors'], 'namespace' => $this->namespace], function ($router) {
				require __DIR__ . '/../Http/routes.php';
			});
	}

}
