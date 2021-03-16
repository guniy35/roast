<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentCommissionTable extends Migration
{
	/**
	 * Run the migrations.
	 * 分销商佣金记录表（佣金结算时候记录数据）
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'agent_commission')) {
			Schema::create($prefix . 'agent_commission', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('agent_id')->comment('分销商ID');
				$table->integer('agent_order_id')->comment('分销商订单表ID');
				$table->integer('commission')->default(0)->comment('佣金 单位为分');
				$table->string('note', 255)->nullable()->comment('描述');
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
		Schema::dropIfExists($prefix . 'agent_commission');
	}
}