<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElGiftCouponTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasTable($prefix . 'gift_coupon')) {
			Schema::create($prefix . 'gift_coupon', function (Blueprint $table) {
				$table->increments('id');
				$table->string('type')->nullable()->comment('gift_new_user   gift_birthday   gift_recharge');
				$table->integer('type_id')->default(0);
				$table->integer('coupon_id')->default(0)->comment('优惠券ID');
				$table->integer('num')->default(1)->comment('数量');
				$table->tinyInteger('status')->default(1)->comment('状态：1 有效 ，0 失效');
				$table->nullableTimestamps();
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

		Schema::drop($prefix . 'gift_coupon');
	}
}
