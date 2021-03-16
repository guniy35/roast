<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsOpenToIbrandDiscountTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasColumn($prefix . 'discount', 'is_open')) {
			Schema::table($prefix . 'discount', function (Blueprint $table) {
				$table->tinyInteger('is_open')->default(1)->after('tags')->comment('是否对外显示');
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
		if (Schema::hasColumn($prefix . 'discount', 'is_open')) {
			Schema::table($prefix . 'discount', function (Blueprint $table) {
				$table->dropColumn('is_open');
			});
		}
	}
}
