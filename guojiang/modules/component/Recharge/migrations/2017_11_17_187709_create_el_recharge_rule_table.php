<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElRechargeRuleTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasTable($prefix . 'recharge_rule')) {
			Schema::create($prefix . 'recharge_rule', function (Blueprint $table) {
				$table->increments('id');
				$table->string('type')->default('gift_recharge');
				$table->string('name')->comment('储值规则名称');
				$table->string('title')->nullable()->comment('前端显示副标题');
				$table->decimal('payment_amount', 15, 2)->nullable()->comment('实付金额');
				$table->decimal('amount', 15, 2)->nullable()->comment('到账金额');
				$table->tinyInteger('status')->default(0)->comment('状态：1 有效 ，0 失效');
				$table->tinyInteger('open_point')->default(0)->comment('赠送积分状态：1 是 ，0 否');
				$table->integer('point')->default(0)->comment('赠送积分');
				$table->tinyInteger('open_coupon')->default(0)->comment('赠送优惠券状态：1 是 ，0 否');
				$table->integer('sort')->default(0)->comment('排序');
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

		Schema::dropIfExists($prefix . 'recharge_rule');
	}
}
