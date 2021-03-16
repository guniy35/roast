<?php

/*
 * This file is part of ibrand/payment.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Payment\Providers;

use GuoJiangClub\Component\Payment\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{

	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../../migrations');
		}

		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../../config/payment.php' => config_path('ibrand/payment.php'),
			]);
		}
	}

	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../../config/payment.php', 'ibrand.payment'
		);

		$this->app->bind('ibrand.pay.notify.default', PaymentService::class);
	}
}
