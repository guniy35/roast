<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsLimitTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'goods_limit')) {
			Schema::create($prefix . 'goods_limit', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('goods_id')->comment('商品id');
				$table->tinyInteger('activity')->default(0)->comment('是否参与限购 0：不参与  1：参与');
				$table->timestamp('starts_at')->nullable()->comment('限购开始时间');
				$table->timestamp('ends_at')->nullable()->comment('限购结束时间');
				$table->integer('quantity')->default(0)->comment('限购数量');
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
		Schema::dropIfExists($prefix . 'goods_limit');
	}
}
