<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeckillTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasTable($prefix . 'seckill')) {
			Schema::create($prefix . 'seckill', function (Blueprint $table) {
				$table->increments('id');
				$table->string('title')->comment('活动标题');
				$table->tinyInteger('status')->default(1)->comment('状态：0 无效；1有效');
				$table->integer('auto_close')->default(0)->comment('拍下多少分钟未付款自动关闭订单：0 采用商城统一设置；>0 使用单独设置');
				$table->dateTime('starts_at')->comment('开始时间');
				$table->dateTime('ends_at')->comment('结束时间');
				$table->string('tags')->nullable()->comment('活动标签');
				$table->timestamps();
				$table->softDeletes();
			});
		}

		if (!Schema::hasTable($prefix . 'seckill_item')) {
			Schema::create($prefix . 'seckill_item', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('seckill_id');
				$table->integer('item_id')->comment('商品ID');
				$table->decimal('seckill_price', 15, 2)->comment('秒杀价格');
				$table->tinyInteger('status')->default(0)->comment('参与状态：0 不参与；1 参与');
				$table->integer('limit')->default(0)->comment('限购数量：0 不限购；');
				$table->tinyInteger('get_point')->default(0)->comment('是否可获得积分：0 否；1是');
				$table->tinyInteger('use_point')->default(0)->comment('是否可使用积分：0 否；1是');
				$table->integer('rate')->default(0)->comment('佣金比例');
				$table->string('img')->nullable();
				$table->integer('sell_num')->default(0);
				$table->integer('sort')->default(9);
				$table->tinyInteger('recommend')->default(0)->comment('是否推荐');
				$table->timestamps();
				$table->softDeletes();
			});
		}

		if (!Schema::hasTable($prefix . 'seckill_sale')) {
			Schema::create($prefix . 'seckill_sale', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('user_id');
				$table->integer('seckill_item_id');
				$table->integer('quantity');
				$table->index(['user_id', 'seckill_item_id']);
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
		Schema::dropIfExists($prefix . 'seckill');
		Schema::dropIfExists($prefix . 'seckill_item');
		Schema::dropIfExists($prefix . 'seckill_sale');
	}
}
