<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRedeemPointToGoodsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasColumn($prefix . 'goods', 'redeem_point')) {
			Schema::table($prefix . 'goods', function (Blueprint $table) {
				$table->integer('redeem_point')->default(0);
			});
		}

		if (!Schema::hasColumn($prefix . 'order', 'redeem_point')) {
			Schema::table($prefix . 'order', function (Blueprint $table) {
				$table->integer('redeem_point')->default(0)->after('total');
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
		if (Schema::hasColumn($prefix . 'goods', 'redeem_point')) {
			Schema::table($prefix . 'goods', function (Blueprint $table) {
				$table->dropColumn('redeem_point');
			});
		}

		if (Schema::hasColumn($prefix . 'order', 'redeem_point')) {
			Schema::table($prefix . 'order', function (Blueprint $table) {
				$table->dropColumn('redeem_point');
			});
		}
	}
}
