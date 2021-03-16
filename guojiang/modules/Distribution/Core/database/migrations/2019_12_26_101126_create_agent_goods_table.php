<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentGoodsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'agent_goods')) {
			Schema::create($prefix . 'agent_goods', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('goods_id')->comment('商品ID');
				$table->tinyInteger('activity')->default(1)->comment('是否参与推广 0：不参与  1：参与');
				$table->float('rate')->comment('佣金比例');
				$table->float('rate_organ')->default(0)->comment('机构推客佣金比例');
				$table->float('rate_shop')->default(0)->comment('门店推客佣金比例');
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
		Schema::dropIfExists($prefix . 'agent_goods');
	}
}