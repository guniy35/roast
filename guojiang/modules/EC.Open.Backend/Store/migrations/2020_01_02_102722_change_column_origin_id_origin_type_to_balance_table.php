<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnOriginIdOriginTypeToBalanceTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$prefix = config('ibrand.app.database.prefix', 'ibrand_');
		if (Schema::hasColumn($prefix . 'balance', 'origin_id')) {
			Schema::table($prefix . 'balance', function (Blueprint $table) {
				$table->integer('origin_id')->default(0)->change();
			});
		}

		if (Schema::hasColumn($prefix . 'balance', 'origin_type')) {
			Schema::table($prefix . 'balance', function (Blueprint $table) {
				$table->string('origin_type')->nullable()->change();
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
