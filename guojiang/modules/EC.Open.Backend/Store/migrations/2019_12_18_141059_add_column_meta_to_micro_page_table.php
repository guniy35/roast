<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMetaToMicroPageTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');

		if (Schema::hasTable($prefix . 'micro_page') && !Schema::hasColumn($prefix . 'micro_page', 'meta')) {
			Schema::table($prefix . 'micro_page', function (Blueprint $table) {
				$table->text('meta')->nullable();
			});
		}

		if (Schema::hasTable($prefix . 'micro_page_advert') && !Schema::hasColumn($prefix . 'micro_page_advert', 'meta')) {
			Schema::table($prefix . 'micro_page_advert', function (Blueprint $table) {
				$table->text('meta')->nullable();
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
		//
	}
}
