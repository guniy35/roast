<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentOrderTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasTable($prefix . 'agent_order')) {
			Schema::create($prefix . 'agent_order', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('agent_id');
				$table->integer('from_agent_id')->default(0)->comment('来源分销商id');
				$table->integer('level')->default(0)->comment('来源分销商层级');
				$table->string('agent_order_no', 64)->comment('分销订单编号');
				$table->integer('order_id')->comment('订单id');
				$table->integer('total_commission')->default(0)->comment('总佣金 单位为分');
				$table->integer('commission')->default(0)->comment('预计可获得佣金 单位为分');
				$table->tinyInteger('status')->default(0)->comment('佣金状态: 0、未结算 1、已结算  2、已失效');
				$table->dateTime('settle_time')->nullable()->comment('结算时间');
				$table->integer('settle_days')->nullable()->default(0)->comment('结算周期：用于猫大手动添加佣金记录');
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

		Schema::dropIfExists($prefix . 'agent_order');
	}
}