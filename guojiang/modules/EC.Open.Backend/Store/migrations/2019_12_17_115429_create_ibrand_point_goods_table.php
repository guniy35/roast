<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbrandPointGoodsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'point_goods')) {
			Schema::create($prefix . 'point_goods', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('item_id')->comment('商品ID');
				$table->tinyInteger('type')->comment('积分计算方式');
				$table->tinyInteger('status')->comment('开启状态');
				$table->integer('value')->comment('积分变化参数');
				$table->tinyInteger('can_use_point')->default(1)->comment('是否可使用积分抵扣');
				$table->string('note')->nullable()->comment('描述');
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

		Schema::dropIfExists($prefix . 'point_goods');
	}
}
