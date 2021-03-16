<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuitsTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasTable($prefix . 'suit')) {
			Schema::create($prefix . 'suit', function (Blueprint $table) {
				$table->increments('id');
				$table->string('img')->nullable()->comment('套餐分享海报');
				$table->tinyInteger('type')->default(1)->comment('套餐类型预留');
				$table->string('title')->nullable()->comment('套餐标题');
				$table->decimal('origin_total', 15, 2)->nullable()->comment('套餐组原总价');
				$table->decimal('total', 15, 2)->nullable()->comment('套餐组合总价');
				$table->text('describe')->nullable()->comment('套餐描述');
				$table->timestamp('starts_at')->nullable()->comment('套餐开始时间');
				$table->timestamp('ends_at')->nullable()->comment('套餐结束时间');
				$table->tinyInteger('status')->default(1)->comment('状态：1 有效 ，0 失效');
				$table->tinyInteger('get_point')->default(0)->comment('是否可获得积分：0 否；1是');
				$table->integer('rate')->default(0)->comment('佣金比例');    //
				$table->tinyInteger('use_point')->default(0)->comment('是否可使用积分：0 否；1是');
				$table->tinyInteger('recommend')->default(0)->comment('是否推荐');
				$table->nullableTimestamps();
				$table->softDeletes();
			});
		}

		if (!Schema::hasTable($prefix . 'suit_item')) {
			Schema::create($prefix . 'suit_item', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('suit_id')->nullable()->comment('套餐表id');
				$table->integer('item_id')->default(0);
				$table->string('item_type')->default('spu');
				$table->decimal('origin_price', 15, 2)->nullable()->comment('原价');
				$table->decimal('package_price', 15, 2)->nullable()->comment('套餐搭配价');
				$table->tinyInteger('fixed')->default(1)->comment('是否必选商品');
				$table->integer('quantity')->default(1)->comment('搭配数量');
				$table->integer('sort')->default(0)->comment('套餐中商品排序');
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

		Schema::dropIfExists($prefix . 'suit');
		Schema::dropIfExists($prefix . 'suit_item');
	}
}
