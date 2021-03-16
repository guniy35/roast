<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIbrandArticleTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasTable($prefix . 'article')) {
			Schema::create($prefix . 'article', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('type')->comment('文章类型');
				$table->string('title')->comment('文章标题');
				$table->string('sub_title')->nullable()->comment('文章副标题');
				$table->string('author')->nullable()->comment('文章发布人');
				$table->string('author_title')->nullable()->comment('发布人头衔');
				$table->string('author_avatar')->nullable()->comment('发布人头像');
				$table->string('img')->comment('展示图片');
				$table->text('goods')->nullable()->comment('关联商品');
				$table->longText('article_detail')->comment('文章详情');
				$table->tinyInteger('is_recommend')->default(0)->comment('是否推荐');
				$table->tinyInteger('status')->default(1)->comment('发布状态');

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
		Schema::dropIfExists($prefix . 'article');
	}
}
