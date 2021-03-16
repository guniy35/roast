<?php

/*
 * This file is part of ibrand/balance.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Component\Balance;

use Event;
use GuoJiangClub\Component\Balance\Listeners\BalanceOrderPaidSuccess;
use GuoJiangClub\Component\Balance\Listeners\BalanceRefundEventListener;
use Illuminate\Support\ServiceProvider;

class BalanceServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}

		Event::subscribe(BalanceOrderPaidSuccess::class);
		Event::subscribe(BalanceRefundEventListener::class);
	}

	/**
	 * Register the service provider.
	 */
	public function register()
	{
	}
}
