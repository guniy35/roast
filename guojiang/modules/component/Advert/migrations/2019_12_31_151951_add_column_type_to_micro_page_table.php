<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeToMicroPageTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (Schema::hasTable($prefix . 'micro_page') && !Schema::hasColumn($prefix . 'micro_page', 'type')) {
			Schema::table($prefix . 'micro_page', function (Blueprint $table) {
				$table->string('type')->default('default')->after('code')->comment('类型');
			});
		}

		if (Schema::hasTable($prefix . 'micro_page') && !Schema::hasColumn($prefix . 'micro_page', 'meta')) {
			Schema::table($prefix . 'micro_page', function (Blueprint $table) {
				$table->text('meta')->nullable()->after('link_image');
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

		Schema::table($prefix . 'micro_page', function (Blueprint $table) {
			$table->dropColumn('type');
			$table->dropColumn('meta');
		});
	}
}
