<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscoverContentTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'discover_content')) {
			Schema::create($prefix . 'discover_content', function (Blueprint $table) {
				$table->increments('id');
				$table->text('description');
				$table->text('img_list')->nullable()->comment('展示图片');
				$table->integer('recommend_goods_id')->default(0)->comment('关联商品id');
				$table->integer('brand_id')->default(0)->comment('所属品牌');
				$table->integer('discover_category_id')->default(0)->comment('所属分类');
				$table->text('tags_list')->nullable()->comment('标签');
				$table->text('meta')->nullable()->comment('用户数据');
				$table->tinyInteger('status')->default(1)->comment('状态');
				$table->tinyInteger('is_recommend')->default(0)->comment('是否推荐');
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
		Schema::dropIfExists($prefix . 'discover_content');
	}
}
