<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElGiftCouponReceiveTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasTable($prefix . 'gift_coupon_receive')) {
			Schema::create($prefix . 'gift_coupon_receive', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('type_id')->nullable();
				$table->string('type')->nullable();
				$table->integer('balance_order_id')->nullable();
				$table->integer('user_id')->nullable();
				$table->integer('discount_id')->nullable();
				$table->integer('gift_coupon_id')->nullable();
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

		Schema::drop($prefix . 'gift_coupon_receive');
	}
}
