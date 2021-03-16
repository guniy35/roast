<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentOrderItemTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'agent_order_item')) {
			Schema::create($prefix . 'agent_order_item', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('agent_id')->comment('分销商id');
				$table->integer('order_item_id')->comment('订单item ID');
				$table->integer('agent_order_id')->comment('分销商订单表ID');
				$table->float('rate')->comment('佣金比例');
				$table->integer('total_commission')->default(0)->comment('总佣金 单位为分');
				$table->integer('commission')->default(0)->comment('预计可获得佣金');
				$table->tinyInteger('status')->default(0)->comment('佣金状态: 0、未结算 1、已结算  2、已失效');
				$table->dateTime('settle_time')->nullable()->comment('结算时间');
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
		Schema::dropIfExists($prefix . 'agent_order_item');
	}
}