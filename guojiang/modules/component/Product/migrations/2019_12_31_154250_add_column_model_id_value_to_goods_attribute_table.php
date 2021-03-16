<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnModelIdValueToGoodsAttributeTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (!Schema::hasColumn($prefix . 'goods_attribute', 'model_id')) {
			Schema::table($prefix . 'goods_attribute', function (Blueprint $table) {
				$table->integer('model_id')->default(0)->after('id');
			});
		}

		if (!Schema::hasColumn($prefix . 'goods_attribute', 'value')) {
			Schema::table($prefix . 'goods_attribute', function (Blueprint $table) {
				$table->text('value')->nullable()->after('name');
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

		Schema::table($prefix . 'goods_attribute', function (Blueprint $table) {
			$table->dropColumn('model_id');
			$table->dropColumn('value');
		});
	}
}
