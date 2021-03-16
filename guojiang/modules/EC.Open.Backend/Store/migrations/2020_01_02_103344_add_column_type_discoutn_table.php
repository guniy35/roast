<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeDiscoutnTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (!Schema::hasColumn($prefix . 'discount', 'type')) {
			Schema::table($prefix . 'discount', function (Blueprint $table) {
				$table->tinyInteger('type')->default(0)->after('code');
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
		if (Schema::hasColumn($prefix . 'discount', 'type')) {
			Schema::table($prefix . 'discount', function (Blueprint $table) {
				$table->dropColumn('type');
			});
		}
	}
}
