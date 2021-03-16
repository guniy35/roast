<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToGoodsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasColumn($prefix . 'goods', 'collocation')) {
			Schema::table($prefix . 'goods', function (Blueprint $table) {
				$table->text('collocation')->nullable()->after('is_new');
			});
		}

		if (!Schema::hasColumn($prefix . 'goods', 'category_group')) {
			Schema::table($prefix . 'goods', function (Blueprint $table) {
				$table->integer('category_group')->default(0);
			});
		}

		if (!Schema::hasColumn($prefix . 'goods', 'is_share')) {
			Schema::table($prefix . 'goods', function (Blueprint $table) {
				$table->tinyInteger('is_share')->default(0);
			});
		}

		if (!Schema::hasColumn($prefix . 'goods', 'is_old')) {
			Schema::table($prefix . 'goods', function (Blueprint $table) {
				$table->tinyInteger('is_old')->default(1);
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
		Schema::table($prefix . 'goods', function (Blueprint $table) {
			$table->dropColumn('collocation');
			$table->dropColumn('category_group');
			$table->dropColumn('is_share');
			$table->dropColumn('is_old');
		});
	}
}
