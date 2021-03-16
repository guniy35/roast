<?php

/*
 * This file is part of ibrand/EC-Open-Core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\EC\Open\Core\Providers;

use GuoJiangClub\Component\Gift\GiftServiceProvider;
use GuoJiangClub\Component\Advert\AdvertServiceProvider;
use GuoJiangClub\Component\Balance\BalanceServiceProvider;
use GuoJiangClub\Component\Discount\Contracts\AdjustmentContract;
use GuoJiangClub\Component\Discount\Providers\DiscountServiceProvider;
use GuoJiangClub\Component\Favorite\FavoriteServiceProvider;
use GuoJiangClub\Component\MultiGroupon\MultiGrouponServiceProvider;
use GuoJiangClub\Component\Order\Models\Adjustment;
use GuoJiangClub\Component\Order\Providers\OrderServiceProvider;
use GuoJiangClub\Component\Payment\Providers\PaymentServiceProvider;
use GuoJiangClub\Component\Point\PointServiceProvider;
use GuoJiangClub\Component\Product\Models\Goods;
use GuoJiangClub\Component\Product\Models\Product;
use GuoJiangClub\Component\Product\ProductServiceProvider;
use GuoJiangClub\Component\Reduce\ReduceServiceProvider;
use GuoJiangClub\Component\Refund\RefundServiceProvider;
use GuoJiangClub\Component\User\Models\User as BaseUser;
use GuoJiangClub\Component\User\UserServiceProvider;
use GuoJiangClub\EC\Open\Core\Auth\User;
use GuoJiangClub\EC\Open\Core\Console\BuildAddress;
use GuoJiangClub\EC\Open\Core\Console\BuildCoupon;
use GuoJiangClub\EC\Open\Core\Discount\Actions\UnitFixedDiscountAction;
use GuoJiangClub\EC\Open\Core\Discount\Actions\UnitPercentageDiscountAction;
use GuoJiangClub\EC\Open\Core\Discount\Checkers\ContainsCategoryRuleChecker;
use GuoJiangClub\EC\Open\Core\Discount\Checkers\ContainsProductRuleChecker;
use GuoJiangClub\EC\Open\Core\PayNotify\RechargePayNotify;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Schema;

class AppServiceProvider extends ServiceProvider
{
	protected $subscribe = [
		'GuoJiangClub\EC\Open\Core\Listeners\OrderEventListener',
		'GuoJiangClub\EC\Open\Core\Listeners\SignDrawEventListener',
		'GuoJiangClub\EC\Open\Core\Listeners\TemplateMessageEventListener',
	];

	/**
	 * Bootstrap any application services.
	 */
	public function boot()
	{
		if (config('ibrand.app.secure')) {
			\URL::forceScheme('https');
		}

		Schema::defaultStringLength(191);

		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../../config/app.php' => config_path('ibrand/app.php'),
			]);
		}

		$this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

		$this->commands([
			BuildAddress::class,
			BuildCoupon::class,
		]);

		foreach ($this->subscribe as $item) {
			Event::subscribe($item);
		}
	}

	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ . '/../../config/app.php', 'ibrand.app'
		);

		$this->registerComponent();

		$this->app->bind(BaseUser::class, User::class);
		$this->app->bind(AdjustmentContract::class, Adjustment::class);
		$this->app->bind(Goods::class, \GuoJiangClub\EC\Open\Core\Models\Goods::class);
		$this->app->bind(Product::class, \GuoJiangClub\EC\Open\Core\Models\Product::class);

		$this->app->bind('ibrand.pay.notify.recharge', RechargePayNotify::class);

		$this->registerDiscountComponent();
	}

	protected function registerComponent()
	{
		$this->app->register(UserServiceProvider::class);
		$this->app->register(ProductServiceProvider::class);
		$this->app->register(BalanceServiceProvider::class);
		$this->app->register(DiscountServiceProvider::class);
		$this->app->register(\GuoJiangClub\Component\Category\ServiceProvider::class);
		$this->app->register(OrderServiceProvider::class);
		$this->app->register(\GuoJiangClub\Component\Address\ServiceProvider::class);
		$this->app->register(\GuoJiangClub\Component\Shipping\ShippingServiceProvider::class);
		$this->app->register(FavoriteServiceProvider::class);
		$this->app->register(AdvertServiceProvider::class);
		$this->app->register(PaymentServiceProvider::class);
		$this->app->register(PointServiceProvider::class);
		$this->app->register(MultiGrouponServiceProvider::class);
		$this->app->register(ReduceServiceProvider::class);
		$this->app->register(GiftServiceProvider::class);
		$this->app->register(RefundServiceProvider::class);
	}

	public function registerDiscountComponent()
	{
		$this->app->bind(
			ContainsCategoryRuleChecker::class,
			ContainsCategoryRuleChecker::class
		);
		$this->app->alias(ContainsCategoryRuleChecker::class, ContainsCategoryRuleChecker::TYPE);

		$this->app->bind(
			ContainsProductRuleChecker::class,
			ContainsProductRuleChecker::class
		);
		$this->app->alias(ContainsProductRuleChecker::class, ContainsProductRuleChecker::TYPE);

		$this->app->bind(
			UnitFixedDiscountAction::class,
			UnitFixedDiscountAction::class
		);
		$this->app->alias(UnitFixedDiscountAction::class, UnitFixedDiscountAction::TYPE);

		$this->app->bind(
			UnitPercentageDiscountAction::class,
			UnitPercentageDiscountAction::class
		);
		$this->app->alias(UnitPercentageDiscountAction::class, UnitPercentageDiscountAction::TYPE);
	}
}
