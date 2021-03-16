<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsUserLimitTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'goods_user_limit')) {
			Schema::create($prefix . 'goods_user_limit', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('goods_id')->comment('商品id');
				$table->integer('user_id')->comment('用户id');
				$table->tinyInteger('buy_nums')->comment('购买数量');
				$table->timestamps();
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
		Schema::dropIfExists($prefix . 'goods_user_limit');
	}
}
