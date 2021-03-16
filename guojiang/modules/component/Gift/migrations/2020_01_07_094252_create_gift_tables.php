<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'gift_activity')) {
			Schema::create($prefix . 'gift_activity', function (Blueprint $table) {
				$table->increments('id');
				$table->string('type')->nullable()->comment('gift_new_user新人礼   gift_birthday生日礼');
				$table->string('name')->comment('活动名称');
				$table->string('title')->nullable()->comment('前端标题');
				$table->tinyInteger('status')->default(0)->comment('状态：1 有效 ，0 失效');
				$table->tinyInteger('open_point')->default(0)->comment('赠送积分状态：1 是 ，0 否');
				$table->integer('point')->default(0)->comment('赠送积分');
				$table->tinyInteger('open_coupon')->default(0)->comment('赠送优惠券状态：1 是 ，0 否');
				$table->timestamp('starts_at')->nullable()->comment('开始时间');
				$table->timestamp('ends_at')->nullable()->comment('结束时间');
				//积分翻倍
				$table->string('point_double')->default(1)->comment('积分倍数目');
				$table->integer('activity_day')->default(1)->comment('有效天数。  从生日当天往推前');
				$table->timestamps();
				$table->softDeletes();

			});

		}

		if (!Schema::hasTable($prefix . 'gift_directional_coupon')) {
			Schema::create($prefix . 'gift_directional_coupon', function (Blueprint $table) {
				$table->increments('id');
				$table->string('directional_type')->nullable()->comment('mobile手机号  custom自定义');
				$table->string('name')->nullable()->comment('活动名称');
				$table->tinyInteger('status')->default(1)->comment('状态：1 有效 ，0 失效');
				$table->text('mobile')->nullable()->comment('手机号');
				$table->string('group_id')->nullable()->comment('会员等级');
				$table->string('n_day_buy')->nullable()->comment('N天内有购买');
				$table->string('n_day_no_buy')->nullable()->comment('N天内有无购买');
				$table->string('buy_num_above')->nullable()->comment('累计购物次数大于');
				$table->string('buy_num_below')->nullable()->comment('累计购物次数小于');
				$table->decimal('buy_price_above', 15, 2)->nullable()->comment('购买商品价格大于');
				$table->decimal('buy_price_below', 15, 2)->nullable()->comment('购买商品价格小于');
				$table->string('number')->nullable()->comment('预计发送人数');
				$table->integer('coupon_id')->default(0)->comment('优惠券ID');
				$table->integer('vip_id')->default(0);
				$table->timestamps();
				$table->softDeletes();
			});
		}


		if (!Schema::hasTable($prefix . 'gift_coupon_receive')) {
			Schema::create($prefix . 'gift_coupon_receive', function (Blueprint $table) {
				$table->increments('id');
				$table->string('type')->nullable();
				$table->integer('type_id')->nullable();
				$table->integer('balance_order_id')->nullable();
				$table->integer('user_id')->nullable();
				$table->integer('discount_id')->nullable();
				$table->integer('gift_coupon_id')->nullable();
				$table->timestamps();
				$table->softDeletes();
			});
		}


		if (!Schema::hasTable($prefix . 'gift_coupon')) {
			Schema::create($prefix . 'gift_coupon', function (Blueprint $table) {
				$table->increments('id');//
				$table->string('type')->default('')->comment('gift_new_user   gift_birthday   gift_recharge');
				$table->integer('type_id')->default(0);
				$table->integer('coupon_id')->default(0)->comment('优惠券ID');
				$table->integer('num')->default(1)->comment('数量');
				$table->tinyInteger('status')->default(1)->comment('状态：1 有效 ，0 失效');
				$table->timestamps();
				$table->softDeletes();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		Schema::dropIfExists($prefix . 'gift_activity');
		Schema::dropIfExists($prefix . 'gift_directional_coupon');
		Schema::dropIfExists($prefix . 'gift_coupon');
		Schema::dropIfExists($prefix . 'gift_coupon_receive');

	}
}
