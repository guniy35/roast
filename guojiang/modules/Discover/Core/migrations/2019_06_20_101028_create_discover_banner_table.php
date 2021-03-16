<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscoverBannerTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasTable($prefix . 'discover_banner')) {
			Schema::create($prefix . 'discover_banner', function (Blueprint $table) {
				$table->increments('id');
				$table->string('img');
				$table->string('keywords')->nullable()->comment('搜索关键字');
				$table->integer('brand_id')->default(0)->comment('所属品牌');
				$table->integer('discover_category_id')->default(0)->comment('所属分类');
				$table->text('tags_list')->nullable()->comment('标签');
				$table->tinyInteger('status')->default(1)->comment('状态');
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
		Schema::dropIfExists($prefix . 'discover_banner');
	}
}
